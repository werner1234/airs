<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/12/02 14:24:39 $
    File Versie         : $Revision: 1.7 $

    $Log: AE_cls_Email.php,v $
  naar RVV 20210120


*/

class AE_cls_Email
{
  var $mailbox;
  var $status = "";
  var $user = "";
  var $error  = false;
  var $messageCount = 0;
  var $messageArray = array();
  var $headerArray = array();
  var $rawMailArray = array();
  var $bodyArray = array();
  var $attachments = array();
  var $db;
  var $matchStats = array();
  var $matchDelimiter = "##";
  var $ownDomain = "";

  function AE_cls_Email()
  {
    $this->user = $_SESSION["USR"];
    $this->db = new DB();
    $cfg = new AE_config();
    $data = array();
    $data['ddbMailServer'] = $cfg->getData('ddbMailServer');
    $data['ddbMailUser']   = $cfg->getData('ddbMailUser');
    $data['ddbMailPasswd'] = $cfg->getData('ddbMailPasswd');
    $this->ownDomain = trim(strtolower($cfg->getData("ddbOwnDomain")));
    //  29-8-2018
    //  drie laatste parameters toegevoegd bij SLV vanwege foutmelding vanuit exchange server
    //  $this->mailbox = imap_open($data['ddbMailServer'],$data['ddbMailUser'], $data['ddbMailPasswd'], 0, 0, array('DISABLE_AUTHENTICATOR' => 'GSSAPI'));
    $this->mailbox = imap_open($data['ddbMailServer'],$data['ddbMailUser'], $data['ddbMailPasswd']);
    if(!$this->mailbox)
    {
      $this->status = "Kan niet verbinden met ".$data['ddbMailServer']." op te zetten. ".imap_last_error()."<br>\n";
      $this->error = true;
    }
    else
    {
      $this->status = "Verbonden als <b>".$data['ddbMailUser']."</b><br>\n";
      $this->error = false;
    }
  }

  function aeClsEmailInit ()
  {
    $this->user = $_SESSION["USR"];
    $this->db = new DB();
    $cfg = new AE_config();
  }

  function messageCount()
  {
    $this->messageCount = imap_num_msg($this->mailbox);
    return $this->messageCount;
  }

  function getMessages()
  {
    $this->user = "daemon";
    $this->clearStore();
    $index = 0;
    $this->headerArray  = array();
    $this->messageArray = array();
    $this->bodyArray    = array();
    $this->rawMailArray = array();
    for ($x = 1; $x <= $this->messageCount(); $x++)
    {
      $result = $this->analyzeMail($x);

      $msg = array();

      $header = imap_fetchheader($this->mailbox, $x);
      $body = imap_body($this->mailbox, $x);
      $uid = imap_uid($this->mailbox, $x);
      $msg["header"]  = imap_rfc822_parse_headers($header);

      if (strtolower($msg["header"]->from[0]->host) == strtolower($this->ownDomain)) //call 9213
      {
        $msg["from"]    = $msg["header"]->to[0]->mailbox . '@' . $msg["header"]->to[0]->host;
      }
      else
      {
        $msg["from"]    = $msg["header"]->from[0]->mailbox . '@' . $msg["header"]->from[0]->host;
      }

//      $msg["from"]    = $msg["header"]->from[0]->mailbox . '@' . $msg["header"]->from[0]->host;
      $msg["stamp"]   = date("Y-m-d H:i:s",strtotime($msg["header"]->date));
      $tmp=imap_mime_header_decode($msg["header"]->subject);
      $subject = '';
      foreach ($tmp as $textObject)
      {
        if ($textObject->charset == 'UTF-8' || $textObject->charset == 'default')
        {
          $subject .= $textObject->text;
        }
        else
        {
          $subject .= iconv($textObject->charset, "UTF-8", $textObject->text);
        }
        $msg["subject"] = $subject;
      }
      $msg["index"] = $uid;
      unset($msg["header"]);
      $this->messageArray[$index] = $msg;
      $this->headerArray[$index]  = imap_fetchheader($this->mailbox, $x);
      $this->bodyArray[$index]    = $result["content"];
      $this->attachments[$index]  = $result["attachments"];
      $this->rawMailArray[$index] = $header."\n\n".$body;
      $this->storeMail($index);
      $index++;
      imap_delete($this->mailbox,$x);
    }
    imap_expunge($this->mailbox);
    imap_close($this->mailbox);
  }

  function populateInbox()
  {
    $this->clearStore();
    $index = 0;
    $this->headerArray  = array();
    $this->messageArray = array();
    $this->bodyArray    = array();
    for ($x = 1; $x <= $this->messageCount(); $x++)
    {
      $result = $this->analyzeMail($x);

      $msg = array();

      $header = imap_fetchheader($this->mailbox, $x);
      global $__debug;
      $__debug =true;

      $msg["header"]  = imap_rfc822_parse_headers($header);
//      debug($msg["header"]);
      if (strtolower($msg["header"]->from[0]->host) == strtolower($this->ownDomain)) //call 9213
      {
        $msg["from"]    = $msg["header"]->to[0]->mailbox . '@' . $msg["header"]->to[0]->host;
      }
      else
      {
        $msg["from"]    = $msg["header"]->from[0]->mailbox . '@' . $msg["header"]->from[0]->host;
      }


      $msg["stamp"]   = date("Y-m-d H:i:s",strtotime($msg["header"]->date));
      $tmp=imap_mime_header_decode($msg["header"]->subject);
      $subject = '';
      foreach ($tmp as $textObject)
      {
        if ($textObject->charset == 'UTF-8' || $textObject->charset == 'default')
        {
          $subject .= $textObject->text;
        }
        else
        {
          $subject .= iconv($textObject->charset, "UTF-8", $textObject->text);
        }
        $msg["subject"] = $subject;
      }
      $msg["index"] = imap_uid($this->mailbox, $x);
      unset($msg["header"]);
      $this->messageArray[$index] = $msg;
      $this->headerArray[$index]  = imap_fetchheader($this->mailbox, $x);
      $this->bodyArray[$index]    = $result["content"];
      $this->attachments[$index]  = $result["attachments"];
      $this->storeMail($index);
      $index++;
    }
  }

  function populateQueue()
  {
    $query = "SELECT `id`, `stamp`, `group`, `from`, `subject`, `route` FROM `dd_mailQueue` ORDER BY  `stamp`";
    $this->db->executeQuery($query);
    while ($rec = $this->db->nextRecord())
    {
      $spl = explode("|",$rec["route"]);
      $rou = count(explode("\n",$rec["route"]));
      
      $rec["CRM_id"] = $spl[2];
      $rec["CRM_naam"] = $spl[3];
      if ($rou == 2)
      {
        $rows["matchSingle"][] = $rec;
      }
      elseif ($rou < 2)
      {
        $rows["noMatch"][] = $rec;
      }
      else
      {
        $rows["matchMulti"][] = $rec;
      }

    }

    return $rows;
  }

  function errorState()
  {
    if ($this->error)
    {
      return $this->status;
    }
    else
    {
      return false;
    }
  }

  function lastStatus()
  {
    return $this->status;
  }

  function findMatches($mailRec)
  {
    $out = array();
    $db = new DB();

    if (strstr($mailRec["subject"],$this->matchDelimiter))
    {
      $portefeuille = explode($this->matchDelimiter,$mailRec["subject"]);
      $query = "SELECT * FROM `dd_mailRouter` WHERE `search` = '".trim($portefeuille[0])."' ORDER BY prio";
      $db->executeQuery($query);
      while ($rec = $db->nextRecord())
      {
        $out[] = $rec;
      }
    }
    if (count($out) > 0 )
    {
      return $out;
    }
    else
    {
      $query = "SELECT * FROM `dd_mailRouter` WHERE `search` = '".trim($mailRec["from"])."' ORDER BY prio";
      $db->executeQuery($query);
      while ($rec = $db->nextRecord())
      {
        $out[] = $rec;
      }
      return $out;
    }

  }

  function matchMails()
  {
    $this->matchStats = array(
      "match"      => 0,
      "partMatch"  => 0,
      "noMatch"    => 0
    );

    $query = "SELECT * FROM `_mailbox` WHERE add_user='daemon'";
    $this->db->executeQuery($query);
    while ($rec = $this->db->nextRecord())
    {
      $matches = $this->findMatches($rec);

      if (count($matches) > 0)
      {
        $route = "";
        define("_fd_","|", true);    // field delimter
        define("_rd_","\n", true);   // row delimter
        $partner = false;
        for ($x=0; $x < count($matches); $x++)
        {
          $m = $matches[$x];


          if ($route <> "" AND $m["prio"] > 2)
          {
            continue;
            // parnter E-mail overslaan als er al een mail adres is gevonden
          }
          else
          {
            $route .= $m["prio"] . _fd_ . $m["search"] . _fd_ . $m["CRM_naw_id"] . _fd_ . $m["CRM_naw_naam"] . _rd_;
          }
          if ($m["prio"] > 2) { $partner = true; }
        }
        $matchTxt = ($partner)?"partMatch":"match";
        $this->addToQueue($rec, $matchTxt, $route);
        $this->matchStats[$matchTxt]++;

      }
      else
      {
        $this->addToQueue($rec, "noMatch");
        $this->matchStats["noMatch"]++;
      }
    }
  }

  function storeInDigidoc($queueId, $crmId, $category="email")
  {
    $dd = new digidoc();

    $queueMsg = $this->getQueueMsgById($queueId);
    $stamp = str_replace(" ","_",substr($queueMsg["stamp"],0,16));
    $recordArray = array (
      "blobdata"    => $queueMsg["rawMail"],
      "filename"    => $stamp."_ingelezen_mailbericht.eml",
      "filesize"    => strlen($queueMsg["rawMail"]),
      "filetype"    => "message/rfc822",
      "description" => mysql_real_escape_string(trim($queueMsg["subject"])),
      "categorie"   => $category,
      "module"      => "CRM_naw",
      "module_id"   => "$crmId"
    );

   return $dd->addDocumentToStore($recordArray);

  }

  function resetQueue()
  {
    $db = new DB();
    $query = "DELETE FROM `dd_mailQueue` WHERE add_user = '".$this->user."'";
    $db->executeQuery($query);
  }

  function getQueueMsgById($mid)
  {
    $db = new DB();
    $query = "SELECT * FROM `dd_mailQueue` WHERE id = $mid";

    $rec = $db->lookupRecordByQuery($query);
    return $rec;
  }

  function deleteFromQueue($ids)
  {
    if (count($ids) < 1)
    {
      return false;
    }
    $db = new DB();
    $query = "DELETE FROM `dd_mailQueue` WHERE id IN (".implode(", ",$ids).")";
    $db->executeQuery($query);
  }


  function addToQueue($mailRec, $group, $route="" )
  {
    $db = new DB();
    $query = "
      INSERT INTO `dd_mailQueue` SET
          `add_user` = '".$this->user."'
        , `add_date` = NOW()
        , `change_user` = '".$this->user."'
        , `change_date` = NOW()
        , `index` = '".$mailRec["index"]."'
        , `stamp` = '".$mailRec["stamp"]."' 
        , `from` = '".mysql_real_escape_string($mailRec["from"])."'
        , `subject` = '".mysql_real_escape_string($mailRec["subject"])."'
        , `body` = UNHEX('".bin2hex($mailRec["body"])."')
        , `group` = '".$group."'
        , `route` = '".mysql_real_escape_string($route)."'
        , `rawMail` = UNHEX('".bin2hex($mailRec["rawMail"])."')
      ";

    $db->executeQuery($query);
  }

  function addCronLog($data)
  {
    $db = new DB();
    $query = "
    INSERT INTO `dd_mailCronLog` SET 
        `add_user`    = '".$this->user."'
      , `add_date`    = NOW()
      , `change_user` = '".$this->user."'
      , `change_date` = NOW()
      , `stamp`       = NOW() 
      , `route`       = '".mysql_real_escape_string($data["route"])."' 
      , `CRM_naam`    = '".mysql_real_escape_string($data["CRM_naam"])."' 
      , `CRM_id`      = '".mysql_real_escape_string($data["CRM_id"])."' 
    ";
    $db->executeQuery($query);
  }

  function storeMail($index)
  {
    $m = $this->messageArray[$index];
    $h = $this->headerArray[$index];
    $b = $this->bodyArray[$index];
    $a = $this->attachments[$index];
    $r = ($this->rawMailArray[$index] != "")?$this->rawMailArray[$index]:"empty";

    $query = "
      INSERT INTO `_mailbox` SET
          `add_user` = '".$this->user."'
        , `add_date` = NOW()
        , `change_user` = '".$this->user."'
        , `change_date` = NOW()
        , `index` = '$index'
        , `stamp` = '".$m["stamp"]."' 
        , `from` = '".mysql_real_escape_string($m["from"])."'
        , `subject` = '".mysql_real_escape_string($m["subject"])."'
        , `body` = UNHEX('".bin2hex($b)."')
        , `header` = UNHEX('".bin2hex($h)."')
        , `rawMail` = UNHEX('".bin2hex($r)."')
        , `attachments` = UNHEX('".bin2hex(serialize($a))."')
      ";
//debug($query);
    $this->db->executeQuery($query);

  }

  function clearStore()
  {
    $query = "DELETE FROM _mailbox WHERE add_user = '".$this->user."'";
    $this->db->executeQuery($query);
  }

  function initTables()
  {
    include_once("../classes/AE_cls_SQLman.php");
    $tst = new SQLman();

    $tst->tableExist("_mailbox",true);  // table aanmaken als die nog niet bestaat
    $tst->changeField("_mailbox","index",array("Type"=>"int","Null"=>false));
    $tst->changeField("_mailbox","stamp",array("Type"=>"DATETIME","Null"=>false));
    $tst->changeField("_mailbox","from",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeField("_mailbox","subject",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeField("_mailbox","body",array("Type"=>"LONGBLOB","Null"=>false));
    $tst->changeField("_mailbox","header",array("Type"=>"BLOB","Null"=>false));
    $tst->changeField("_mailbox","attachments",array("Type"=>"LONGBLOB","Null"=>false));
    $tst->changeField("_mailbox","rawMail",array("Type"=>"LONGBLOB","Null"=>false));

    $tst->tableExist("dd_mailRouter",true);  // table aanmaken als die nog niet bestaat
    $tst->changeField("dd_mailRouter","autoRoute",array("Type"=>"tinyint","Null"=>false));
    $tst->changeField("dd_mailRouter","search",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeField("dd_mailRouter","prio",array("Type"=>"int","Null"=>false));
    $tst->changeField("dd_mailRouter","CRM_naw_id",array("Type"=>"int","Null"=>false));
    $tst->changeField("dd_mailRouter","CRM_naw_naam",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeIndex("dd_mailRouter","searchKey",array("columns"=>"search"));

    $tst->tableExist("dd_mailQueue",true);  // table aanmaken als die nog niet bestaat
    $tst->changeField("dd_mailQueue","index",array("Type"=>"int","Null"=>false));
    $tst->changeField("dd_mailQueue","stamp",array("Type"=>"DATETIME","Null"=>false));
    $tst->changeField("dd_mailQueue","from",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeField("dd_mailQueue","subject",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeField("dd_mailQueue","body",array("Type"=>"LONGBLOB","Null"=>false));
    $tst->changeField("dd_mailQueue","header",array("Type"=>"BLOB","Null"=>false));
    $tst->changeField("dd_mailQueue","attachments",array("Type"=>"LONGBLOB","Null"=>false));
    $tst->changeField("dd_mailQueue","route",array("Type"=>"BLOB","Null"=>false));
    $tst->changeField("dd_mailQueue","rawMail",array("Type"=>"LONGBLOB","Null"=>false));
    $tst->changeField("dd_mailQueue","group",array("Type"=>"varchar(100)","Null"=>false));

    $tst->tableExist("dd_mailCronLog",true);  // table aanmaken als die nog niet bestaat
    $tst->changeField("dd_mailCronLog","stamp",array("Type"=>"DATETIME","Null"=>false));
    $tst->changeField("dd_mailCronLog","route",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeField("dd_mailCronLog","CRM_naam",array("Type"=>"varchar(200)","Null"=>false));
    $tst->changeField("dd_mailCronLog","CRM_id",array("Type"=>"int","Null"=>false));


  }

  function buildRouterTable()
  {
    $query = "TRUNCATE `dd_mailRouter` ";
    $this->db->executeQuery($query);
    $query = "
    SELECT 
      email, 
      emailZakelijk, 
      emailPartner, 
      emailPartnerZakelijk, 
      portefeuille,
      id, 
      PortGec,
      concat(naam,' ',naam1,' (',portefeuille,')') as naam 
    FROM 
      CRM_naw 
    WHERE 
      aktief = 1";

    $this->db->executeQuery($query);
    while($rec = $this->db->nextRecord())
    {
      if (trim($rec["portefeuille"]) <> "" AND
               $rec["PortGec"] != 1)                $this->addToRouter(trim($rec["portefeuille"]),1,$rec);   // alleen gebruiken als niet geconsolideerd
      if (trim($rec["email"]) <> "")                $this->addToRouter(trim($rec["email"]),1,$rec);
      if (trim($rec["emailZakelijk"]) <> "")        $this->addToRouter(trim($rec["emailZakelijk"]),2,$rec);
      if (trim($rec["emailPartner"]) <> "")         $this->addToRouter(trim($rec["emailPartner"]),3,$rec);
      if (trim($rec["emailPartnerZakelijk"]) <> "") $this->addToRouter(trim($rec["emailPartnerZakelijk"]),4,$rec);
    }
  }

  function addToRouter($search,$prio=1,$rec)
  {
    $search = str_replace("'", "", $search);
    $db2 = new DB();
    $query = "
    INSERT INTO `dd_mailRouter` SET
      `add_user` = 'daemon',
      `add_date` = NOW(),
      `search`   = '$search',
      `prio` = '$prio',
      `CRM_naw_id` = '".$rec["id"]."',
      `CRM_naw_naam` = '".mysql_real_escape_string($rec["naam"])."'
    ";
    $db2->executeQuery($query);
  }


  function analyzeMail($mid)
  {
    $mbox = $this->mailbox;
    $struct = imap_fetchstructure($mbox, $mid);

    $parts = $struct->parts;
    $i = 0;

    if (!$parts)
    { /* Simple message, only 1 piece */
      $attachment = array(); /* No attachments */
      $content = imap_body($mbox, $mid);
    }
    else
    { /* Complicated message, multiple parts */

      $endwhile = false;

      $stack = array(); /* Stack while parsing message */
      $content = "";    /* Content of message */
      $attachment = array(); /* Attachments */

      while (!$endwhile)
      {

        if (!$parts[$i])
        {
          if (count($stack) > 0)
          {
            $parts = $stack[count($stack)-1]["p"];
            $i     = $stack[count($stack)-1]["i"] + 1;
            array_pop($stack);
          }
          else
          {
            $endwhile = true;
          }
        }

        if (!$endwhile)
        {
          /* Create message part first (example '1.2.3') */
          $partstring = "";
          foreach ($stack as $s)
          {
            $partstring .= ($s["i"]+1) . ".";
          }
          $partstring .= ($i+1);

          if (strtoupper($parts[$i]->disposition) == "ATTACHMENT")
          { /* Attachment */
            if (is_array($parts[$i]->parameters))
            {
              $attachment[] = array("filename" => $parts[$i]->parameters[0]->value,
                                    "filedata" => imap_fetchbody($mbox, $mid, $partstring));

            }

          }
          elseif (strtoupper($parts[$i]->subtype) == "PLAIN" || strtoupper($parts[$i]->subtype) == "HTML")
          { /* Message */
            switch ($parts[$i]->encoding)
            {
              case 1:
                $content .= imap_8bit (imap_fetchbody($mbox, $mid, $partstring));
                break;
              case 3:
                $content .= imap_base64 (imap_fetchbody($mbox, $mid, $partstring));
                break;
              default:
                $content .= imap_qprint(imap_fetchbody($mbox, $mid, $partstring));
            }

          }
        }

        if ($parts[$i]->parts)
        {
          $stack[] = array("p" => $parts, "i" => $i);
          $parts = $parts[$i]->parts;
          $i = 0;
        } else
        {
          $i++;
        }
      } /* while */
    } /* complicated message */

    return array("content"=>$content, "attachments" => $attachment);
  }


}