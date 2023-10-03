<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/20 12:29:55 $
 		File Versie					: $Revision: 1.11 $

 		$Log: AE_cls_secruity.php,v $
 		Revision 1.11  2020/04/20 12:29:55  cvs
 		call 8437
 		
 		Revision 1.10  2020/03/06 15:07:18  cvs
 		call 8437
 		


*/
  

/*
 *  Deze class is tbv het wachtwoordbeleid call 3691
 *
 * extra tabel voor deze class
 *
  $tst = new SQLman();

  $tst->tableExist("GebruikersLogin",true);
  $tst->changeField("GebruikersLogin","userID",array("Type"=>" int","Null"=>false));
  $tst->changeField("GebruikersLogin","userNaam",array("Type"=>" varchar(10)","Null"=>false));
  $tst->changeField("GebruikersLogin","geldigTot",array("Type"=>" date","Null"=>false));
  $tst->changeField("GebruikersLogin","vorigWW",array("Type"=>" varchar(200)","Null"=>false));
  $tst->changeField("GebruikersLogin","huidigWW",array("Type"=>" varchar(200)","Null"=>false));
  $tst->changeField("GebruikersLogin","laatsteLogin",array("Type"=>" datetime","Null"=>false));
  $tst->changeField("GebruikersLogin","laatsteIP",array("Type"=>" varchar(200)","Null"=>false));
  $tst->changeField("GebruikersLogin","laatsteSMScode",array("Type"=>" varchar(20)","Null"=>false));
  $tst->changeField("GebruikersLogin","SMSstamp",array("Type"=>" datetime","Null"=>false));
 */


class AE_cls_secruity
{
  var $wwDuur = 0;
  var $wwComplexiteit = "";
  var $fingerPrint;
  var $sessieDuur;
  var $loginRec;
  var $userRec;
  var $user;
  var $twoFactor = false;
  var $tfa       = false;
  var $gracetime = 43200;  // 12 uur in sec (12*3600)
  var $beleid;
  var $smsValidTill = "";
  
  function AE_cls_secruity($user)
  {
    $cfg = new AE_config();
    $db  = new DB();
    $this->fingerPrint    = rand ( 1111111 , 9999999 );
    $this->wwDuur         = $cfg->getData("wwBeleid_livetime");
    $this->twoFactor      = ($cfg->getData("wwBeleid_2factor") == "aan");
    $this->tfa            = ($cfg->getData("wwBeleid_2factor") == "tfa");
    $this->wwComplexiteit = $cfg->getData("wwBeleid_complexiteit");
    $this->sessieDuur     = (isNumeric($cfg->getData("wwBeleid_sessieDuur")))?$cfg->getData("wwBeleid_sessieDuur")*3600:7200;  // via wwb of default 2 uur
    $this->beleid         = $cfg->getData("wwBeleid_soort");  
    $this->user           = $user;
//    debug($this, "construct");
    $query = "SELECT * FROM Gebruikers WHERE Gebruiker = '".mysql_real_escape_string($user)."' ";
    //debug($query);
    if (!$this->userRec = $db->lookupRecordByQuery($query))
    {
      return false;
    }
    $this->userRec['orderRechten']=unserialize($this->userRec['orderRechten']);
    
    $query = "SELECT *, UNIX_TIMESTAMP(SMSstamp) as smsJul FROM `GebruikersLogin` WHERE userNaam ='".mysql_real_escape_string($user)."' ";
    if (!$this->loginRec = $db->lookupRecordByQuery($query))
    {
      $this->updateLogin();
    }


    // check of SMS ovgeslagen mag worden

    if ( ( ($this->getLoginData("smsJul")+$this->gracetime) > mktime() AND $this->getLoginData("loginSucces") != 0)     // er is eerder binnen de gracperiode ingelogd
            AND
         ($this->getLoginData("laatsteIP") == $_SERVER["REMOTE_ADDR"])      // vanaf hetzelfde IP

       )
    {
      $this->twoFactor = false;
    }

    $this->smsValidTill = date("d-m-Y H:i", $this->getLoginData("smsJul")+$this->gracetime);
    
  }
  
  function updateLogin($options=array())
  {
    global $USR;
    $db = new DB();
    if (!$this->loginRec)
    {
      $query = "
        INSERT INTO GebruikersLogin SET
          add_date  = NOW(), 
          add_user  = '$USR',
          change_date  = NOW(), 
          change_user  = '$USR',
          userID    = '".$this->userRec["id"]."', 
          userNaam  = '".$this->userRec["Gebruiker"]."', 
          geldigTot = '".$this->newGeldigDate()."',
          huidigWW  = '',
          laatsteIP = '".$_SERVER["REMOTE_ADDR"]."'
      ";
      $db->executeQuery($query);
      $query = "SELECT *, UNIX_TIMESTAMP(laatsteLogin) as smsJul FROM `GebruikersLogin` WHERE userNaam ='".$this->userRec["Gebruiker"]."' ";
      $this->loginRec = $db->lookupRecordByQuery($query);
    } 
    if (count($options) > 0)
    {
      $query = "UPDATE GebruikersLogin SET change_date = NOW() ";
      foreach($options as $field=>$value)
      {
        $query .= "\n  , `$field` = '$value'";
      }
      $query .= "\n WHERE userNaam = '".$this->loginRec["userNaam"]."' ";
      $db->executeQuery($query);
    }  
  }
  
  function pwHash($password)
  {
    if (function_exists("hash"))
    {
      return hash("sha512", $password);
    }
    else
    {
      return sha1($password);
    }

  }
  
  function checkGeldigHeid()
  {
    if (trim($this->wwComplexiteit) == "" )  // classic geen checks nodig
    {
      return true;
    }
    
    return (form2jul(dbdate2form($this->getLoginData("geldigTot"))) > mktime())   // ww is nog geldig
              AND
           ($this->getLoginData("huidigWW") <> "");  // er is een ww ingevuld
  }

  function SMSCheck($code, $sent)
  {
    $cfg = new AE_config();
//debug(func_get_args());
//debug($this);
//debug($this->getLoginData("laatsteSMScode"));

    if ($cfg->getData("wwBeleid_2factor_override") == "1") // call 7815: SMS is uitgeschakeld door AIRS om tijdens een SMS storing toch te kunnen inloggen
    {
      return true;
    }

    if ($sent == "false")
    {
      return true;  // SMS offline
    }
    if (($cfg->getData("wwBeleid_2factor") != "uit") AND $sent == "true")  // 2 factor ingesteld
    {
      if ($cfg->getData("wwBeleid_2factor") == "aan")           // SMS twee factor
      {
        if ($code == $this->getLoginData("laatsteSMScode") )
        {
          return true;  // sms accoord
        }
        else
        {
          $this->updateLogin(array( "laatsteSMScode" => "", "SMSstamp" => "0000-00-00"));
          return false; // sms fail
        }
      }
      else                                                      // Google twee factor
      {
        $this->updateLogin(array( "laatsteSMScode" => "", "SMSstamp" => "0000-00-00"));
        return false;                                           // geen TTL altijd laten valideren
      }

    }
    else
    {
      return true;   // geen 2 factor
    }
  }
  
  function getLoginData($field = "all")
  {
    if (!$this->loginRec)
    {
      return false;
    }
    else
    {
      if ($field == "all")
      {
        return $this->loginRec;
      }
      else
      {
        return $this->loginRec[$field];
      }
    }
  }
  
  function getGeldigDate()
  {
    return dbdate2form($this->getLoginData("geldigTot"));
  }
  
  function newGeldigDate()
  {
    if ($this->wwDuur > 0)
    {
      $geldigheidInDagen = ceil($this->wwDuur * 30.5);
      return date("Y-m-d",mktime() + ($geldigheidInDagen * 86400));
    }
    else
    {
      return "2035-12-31";
    }
  }
  
  function deleteLogin($user)
  {
    $db = new DB();
    $query = "DELETE FROM `GebruikersLogin` WHERE userNaam ='".$user."'";

    $db->executeQuery($query);
  }

  function deleteTFA($user)
  {
    $db = new DB();
    $query = "UPDATE  FROM `GebruikersLogin` WHERE userNaam ='".$user."'";

    $db->executeQuery($query);
  }


}

?>