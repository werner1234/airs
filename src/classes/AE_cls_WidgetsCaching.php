<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/03/04 13:32:43 $
    File Versie         : $Revision: 1.3 $

    $Log: AE_cls_WidgetsCaching.php,v $
    Revision 1.3  2020/03/04 13:32:43  cvs
    call 8459

    Revision 1.2  2019/02/04 10:55:16  cvs
    grootte blog verhoogt naar MEDIUMBLOB

    Revision 1.1  2018/09/28 13:05:05  cvs
    widget caching


*/

class AE_cls_WidgetsCaching
{
  var $tableName = "widgetCache";
  var $user    = "";
  var $widget  = "";
  var $content = "";
  var $now     = 0;
  var $expire  = 0;
  var $ttl     = 0;     // cache gebruiken tot minuten na aanmaak
  var $startStamp = 0;

  function AE_cls_WidgetsCaching($widgetName = '', $ttl=180)
  {
    global $USR;
    $this->initModule();
    $this->now        = time();
    $this->startStamp = microtime(true);
    $this->user       = $_SESSION["USR"];
    $this->widget     = $widgetName;
    $this->ttl        = $ttl;
    $this->getCache();
  }

  function useCache()
  {
    return ($this->expire > 0);
  }

  function btnCache()
  {
    return '<button class="btn-new btn-default pull-right fa fa-recycle headSetup" 
                  id="btnCache_'.$this->widget.'" aria-hidden="true" 
                  title="widget ververst over <!--ttl-->"></button>';
  }

  function dataState()
  {
    $db = new DB();
    $query = "
    SELECT 
      id,
      change_date 
    FROM 
      `laatstePortefeuilleWaarde` 
    ORDER BY 
      `change_date` DESC 
    ";
    $rec = $db->lookupRecordByQuery($query);
    $state = (substr($rec["change_date"],0,10) == date("Y-m-d"));

    if ($state)
    {
      $color = "green";
      $title = "De dataset is actueel (".substr($rec["change_date"],11,5).")";
      $iconClass = "fa-plus-circle";
    }
    else
    {
      $fmt = new AE_cls_formatter();
      $date = $fmt->format("@D{form}", substr($rec["change_date"],0,10));
      $color = "red";
      $iconClass = "fa-minus-circle";
      $title = "De dataset is vandaag nog \nniet bijgewerkt (".$date.")";
    }

    return '<button class="btn-new btn-default pull-right fa '.$iconClass.' headSetup" 
                  id="btnCache_'.$this->widget.'" aria-hidden="true" 
                  style="color:'.$color.'; cursor:help" 
                  title="'.$title.'"></button>';
  }

  function updateStamp()
  {
    if ($this->expire == 0)
    {
      return "realtime";
    }
    return ($this->expire." min" );
  }

  function getCache()
  {
    $db = new DB();
    $query = "SELECT ROUND((UNIX_TIMESTAMP(ttl) -UNIX_TIMESTAMP(now()))/60,0) as expire,{$this->tableName}.* FROM {$this->tableName} WHERE widgetName = '{$this->widget}' AND user = '{$this->user}'";
    if ($rec = $db->lookupRecordByQuery($query))
    {

      $this->content = str_replace("{ttl}",$this->updateStamp(),$rec["content"]);
      $this->expire  = $rec["expire"];
    }
    else
    {
      $this->expire  = 0;
      $this->content = "";
    }
  }

  function deleteCache()
  {
    $db = new DB();
    $query = "DELETE FROM {$this->tableName} WHERE widgetName = '{$this->widget}' AND user = '{$this->user}'";
    return $db->executeQuery($query);
  }

  function deleteCacheForUser($user = '')
  {
    if ( empty ($user) ) {
      $user = $this->user;
    }

    $db = new DB();
    $query = "DELETE FROM {$this->tableName} WHERE user = '" . mysql_real_escape_string($user) . "'";
    return $db->executeQuery($query);
  }

  function addToCache($content)
  {

     $generateTime = round(microtime(true) - $this->startStamp,4)." sec";
     $ttl = date("Y-m-d H:i:s",(time() + ($this->ttl * 60)));
     $this->expire = $ttl;
     $content = "<!-- cachefile for {$this->widget}/{$this->user} valid thru $ttl -->\n<!-- generateTime {$generateTime}-->\n".mysql_real_escape_string($content);
     $db = new DB();
     $this->deleteCache();
     $query = "
     INSERT INTO `".$this->tableName."` SET
     `add_user`   = '{$this->user}',
     `add_date`   = NOW(),
     `widgetName` = '{$this->widget}',
     `user`       = '{$this->user}',
     `ttl`        = '{$ttl}',
     `content`    = '{$content}<!-- end of file -->'
     ";
     $db->executeQuery($query);

  }
  function JSinit()
  {
    $out = '
      $("#btnCache_'.$this->widget.'").click(function()
      {
        console.log("btnCache click");
        $.ajax(
          {
            url:"ajax/updateAEconfig.php",
            data:{
              field: "",
              value: "",
              widget: "'.$this->widget.'"
          },
          
          success:function(data)
          {
            console.log("Cache: '.$this->widget.' gereset ");
            console.log("ok");
            location.reload(true);
          }
        });
      });
    ';
    return $out;
  }

  function initModule()
  {
    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"widgetName",array("Type"=>" varchar(40)","Null"=>false));
    $tst->changeField($this->tableName,"user",array("Type"=>" varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,"ttl",array("Type"=>" datetime","Null"=>false));
    $tst->changeField($this->tableName,"content",array("Type"=>" mediumblob","Null"=>false));
  }

  
}