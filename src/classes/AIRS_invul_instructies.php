<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:13 $
    File Versie         : $Revision: 1.4 $

    $Log: AIRS_invul_instructies.php,v $
    Revision 1.4  2018/08/18 12:40:13  rvv
    php 5.6 & consolidatie

    Revision 1.3  2017/09/20 06:12:11  cvs
    megaupdate 2722

    Revision 1.2  2017/03/24 09:35:07  cvs
    call 5731

    Revision 1.1  2016/06/20 08:18:42  cvs
    call 5027 invulinstructies



*/

class AIRS_invul_instructies
{
  var $vermogensBeheerder;
  var $rekeningNr = "";
  var $portefeuille = "";

  function AIRS_invul_instructies($vermogensbeheerder="*")
  {
    $this->vermogensBeheerder = $vermogensbeheerder;
    //$this->initModule();
  }

  function getBeheerderViaRekening($reknr)
  {
    $db = new DB();
    $query = "
    SELECT
      Portefeuilles.Vermogensbeheerder,
      Portefeuilles.Portefeuille,
      Rekeningen.Rekening
    FROM
      Portefeuilles
    INNER JOIN Rekeningen ON 
      Rekeningen.Portefeuille = Portefeuilles.Portefeuille
    WHERE 
      Rekeningen.consolidatie=0 AND
      Rekeningen.Rekening = '$reknr'
    ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      $this->vermogensBeheerder = $rec["Vermogensbeheerder"];
      $this->rekeningNr         = $rec["Rekening"];
      $this->portefeuille       = $rec["Portefeuille"];
    }
    else
    {
      $this->vermogensBeheerder = "*";
      $this->rekeningNr         = "";
      $this->portefeuille       = "";
    }

  }

  function getInstructie($script, $field, $value)
  {
    $db = new DB();
    $query = "
      SELECT 
        * 
      FROM 
        `invulInstructies`
      WHERE
        `vermogensBeheerder` = '".$this->vermogensBeheerder."' AND
        `script`             = '".$script."' AND
        `field`              = '".$field."' AND
        `value`              = '".$value."' AND
        `active`             = 1
        ";
    if (!$out = $db->lookupRecordByQuery($query))
    {
      $query = "
      SELECT 
        * 
      FROM 
        `invulInstructies`
      WHERE
        `vermogensBeheerder` = '".$this->vermogensBeheerder."' AND
        `script`             = '".$script."' AND
        `field`              = '".$field."' AND
        `value`              = '*' AND
        `active`             = 1
        ";
      $out = $db->lookupRecordByQuery($query);
    }
//debug($query);
//    foreach($out as $k=>$v)
//    {
//      $result[$k] = str_replace("\n","<br/>" ,$v);
//      $result[$k] = str_replace("\r","" ,$result[$k]);
//    }
    return $out;
  }

  function getMessageDiv()
  {
    return '
    <div id="jqAE-invul-msg">
      <div id="jqAE-invul-msgHead"></div>
      <div id="jqAE-invul-msgContent"></div>
    </div>
    ';
  }





  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist("invulInstructies",true);
    $tst->changeField("invulInstructies","vermogensBeheerder",array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField("invulInstructies","script",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("invulInstructies","field",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("invulInstructies","value",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("invulInstructies","active",array("Type"=>"tinyint","Null"=>false, "default"=> "DEFAULT 1"));
    $tst->changeField("invulInstructies","header",array("Type"=>"text","Null"=>false));
    $tst->changeField("invulInstructies","text",array("Type"=>"text","Null"=>false));
    $tst->changeField("invulInstructies","class",array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField("invulInstructies","memo",array("Type"=>"text","Null"=>false));

  }

  
}