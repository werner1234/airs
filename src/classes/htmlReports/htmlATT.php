<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2017/05/29 14:13:04 $
    File Versie         : $Revision: 1.13 $

    $Log: htmlATT.php,v $
    Revision 1.13  2017/05/29 14:13:04  rm
    Htmlrapport

    Revision 1.12  2017/01/06 08:08:13  rm
    Html rapportage

    Revision 1.11  2017/01/05 14:27:35  rm
    Html rapportage

    Revision 1.10  2016/12/21 16:34:23  rm
    Html rapport

    Revision 1.9  2016/12/19 07:44:15  rm
    Html rapport

    Revision 1.8  2016/12/02 09:05:54  rm
    Html rapportage

    Revision 1.7  2016/10/21 06:23:42  rm
    Html rapport

    Revision 1.6  2016/10/18 09:08:44  cvs
    update cvs 20161018

    Revision 1.5  2016/10/17 09:33:15  rm
    htmlrapport

    Revision 1.4  2016/09/30 06:32:30  cvs
    call 4848: derde bestand Kasbankl

    Revision 1.3  2016/09/28 14:08:42  rm
    html rapport

    Revision 1.2  2016/09/07 12:00:46  rm
    html rapportage

    Revision 1.1  2016/08/31 13:04:39  cvs
    call 5213



*/

class htmlATT extends AE_cls_htmlColomns
{
  var $user;
  var $tableName = "_htmlRapport_ATT";
  var $portefeuille;
  var $properties = array(
    "type"             => "text",         // soort veld
    "description"      => "",             // kolom omschrijving
    "descriptionShort" => "",             // kolom omschrijving kort
    "widthClass"       => "w10",         // class tbv breedte kolom
    "displayFormat"    => "@S{0}",        // printf formaat
    "formatClass"      => "attText",     // class tbv opmaak
    "negativeClass"    => "attNegative", // class tbv negatieve waarde
    "headerClass"      => "headerLeft",   // header class alignment
    "sumTotal"         => false,          // true als kolom totaliseerbaar is
    "sumClass"         => "attSubTotal", // class tbv opmaak sum waarde
    "sumFormat"        => "@N",         // printf formaat sum waarde
    "position"         => 10,             // kolom positie in matrix
    "sort"             => false,          // kolom is sorteerbaar
    "hideColumn"       => false,
    "visible"          => true,
    "hideEndTotal"     => false,
    "value"            => null,
    'clickable'         => '',

    "links"            => array(),
    "fixed"            => false           // Bij true kan het veld niet verborgen of gesorteerd worden
  );
  var $sortProperties = array(
    "orderField" => "",
    "descriptionField" => "",
    "orderIncremental" => true,
    "disable" => false,
    "prio" => 100,
  );

  var $endTotal;
  
  var $data = array();
  var $db;
  function htmlATT($portefeuille)
  {
    global $USR;
    $this->user = $USR;
    $this->defineData();
    $this->defineSorting();
    $this->defineEndTotal();
    $this->portefeuille = $portefeuille;
    $this->db = new DB($this->dbId);
  }

  
  function clearTable()
  {
    // verwijder historie ouder dan vandaag
    $query = "DELETE FROM `".$this->tableName."` WHERE `add_date` <= '".date("Y-m-d", mktime()-86400)." 23:59:59' ";

    $this->db->executeQuery($query);
    // verwijder historie van portefeuille
    $query = "DELETE FROM `".$this->tableName."` WHERE `add_user` = '".$this->user."' AND portefeuille = '".$this->portefeuille."'";
    $this->db->executeQuery($query);

  }
  
  function addRecord($data)
  {
    $query = "INSERT INTO `".$this->tableName."` SET ";
    $query .= "  add_date = NOW() ";
    $query .= "  ,change_date = NOW() ";
    $query .= "  ,add_user = '".$this->user."' ";
    $query .= "  ,change_user = '".$this->user."' ";
    foreach($data as $k=>$v)
    {
      $query .= "\n  , `$k` = '".addslashes($v)."' ";
    }
    $this->db->executeQuery($query);
  }

  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"soort",array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,"waardeBegin",array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'perfCumulatief',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'index',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'specifiekeIndex',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'valuta',array("Type"=>"varchar(4)","Null"=>false));
    $tst->changeField($this->tableName,'periode',array("Type"=>"varchar(35)","Null"=>false));
    $tst->changeField($this->tableName,'periodeForm',array("Type"=>"varchar(35)","Null"=>false));
    $tst->changeField($this->tableName,'waardeBegin',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'waardeHuidige',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'waardeMutatie',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'stortingen',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'onttrekkingen',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'resultaatVerslagperiode',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'gemiddelde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'kosten',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'opbrengsten',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'performance',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'ongerealiseerd',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'rente',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'gerealiseerd',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'extra' ,array("Type"=>"text","Null"=>false));
    $tst->changeField($this->tableName,'datum',array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,'specifiekeIndexPerformance',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'perfCumulatief',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'portefeuille',array("Type"=>"varchar(24)","Null"=>false));

  }

  function defineEndTotal()
  {
    $this->endTotal = array(
    "negativeClass"    => "endTotalNegative", // class tbv negatieve waarde
    "class"            => "endTotal",         // class tbv opmaak sum waarde
    "format"           => "@N{.0}",           // printf formaat sum waarde
  );
  }

  function defineSorting()
  {
    $this->addSortDef("valuta",array(
       "orderField"       => "valutaVolgorde, valutaOmschrijving",
       "descriptionField" => "valutaOmschrijving",
       "orderBreakField"  => "valutaOmschrijving",
       "orderIncremental" => true,
       "disable"          => false,
       "headerClass"      => "headerTree",
       "prio"             => 1,
    ));


  }
  
  function defineData()
  {
    global $__appvar;

		$this->addColomnDef("soort", array(
      "type"             => "text",   
      "description"      => vt("soort"),
      "descriptionShort" => vt("soort"),
      "widthClass"       => "w10",
      "formatClass"      => "attText",
      "displayFormat"    => "@N",
      "negativeClass"    => "",      
      "position"         => 0,      
      "sort"             => false    
    ));

    $this->addColomnDef("perfCumulatief", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("Cumulatief rendement"),
      "descriptionShort" => vt("Cumulatief rendement"),
      "widthClass"       => "w75", 
      "displayFormat"    => "@N{.2}%",
      "sumFormat"        => "@N{.2}%",
      "sumTotalFormat"   => "@N{.2}%",
      "headerClass"      => "headerRight",
//      "sort"             => true,
      "widthClass"       => "w100",

    ));

    $this->addColomnDef("specifiekeIndex", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("specifiekeIndex"),
    ));

    $this->addColomnDef("valuta", array(
      "type"             => "text",
      "formatClass"      => "attNumber",
      "description"      => vt("valuta"),
    ));

    $this->addColomnDef("periode", array(
      "type"             => "text",
      "formatClass"      => "attNumber",
      "description"      => vt("periode"),
    ));
    $this->addColomnDef("periodeForm", array(
      "type"             => "text",
      "formatClass"      => "attNumber",
      "description"      => vt("periode"),
    ));

    $this->addColomnDef("waardeBegin", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("Beginvermogen"),
      "displayFormat"    => "@N",        // printf formaat
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",
      'clickableSumIf'     => array (
        0 => 'start',
        1 => '>=',
        2 => 'Startdatum',
        3 => 'date'
      ),
      'clickableSum'     => $__appvar['baseurl'] . '/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=VOLK&datum_tot={start}&Portefeuille={portefeuille}', //&datum_tot={stop}
    ));

    $this->addColomnDef("waardeHuidige", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Eindvermogen"),
      "widthClass"       => "w100",
      'clickableSum'         => $__appvar['baseurl'] . '/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=VOLK&datum_tot={stop}&Portefeuille={portefeuille}',//&datum_van={start}
    ));

    $this->addColomnDef("waardeMutatie", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("mutatie"),
      "widthClass"       => "w75",
    ));

    $this->addColomnDef("stortingen", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Stortingen"),
      "sumTotal"         => true,
      "widthClass"       => "w100",
    ));

    $this->addColomnDef("onttrekkingen", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Onttrekkingen"),
      "sumTotal"         => true,
      "widthClass"       => "w100",
    ));

    $this->addColomnDef("resultaatVerslagperiode", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Beleggingsresultaat"),
      "sumTotal"         => true,
      "widthClass"       => "w75",
    ));

    $this->addColomnDef("gemiddelde", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("gemiddelde"),
    ));

    $this->addColomnDef("kosten", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Kosten"),
      "sumTotal"         => true,
      "widthClass"       => "w100",
    ));

    $this->addColomnDef("opbrengsten", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Opbrengsten"),
      "sumTotal"         => true,
      "widthClass"       => "w75",
    ));

    $this->addColomnDef("performance", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N{.2}%",
      "description"      => vt("Rendement"),
      "widthClass"       => "w75",
    ));

      $this->addColomnDef("ongerealiseerd", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Koersresultaat"),
      "sumTotal"         => true,
      "widthClass"       => "w75",
    ));

      $this->addColomnDef("rente", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("Mutatie opgelopen rente"),
      "sumTotal"         => true,
      "widthClass"       => "w100",
    ));

      $this->addColomnDef("gerealiseerd", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("gerealiseerd"),
      "sumTotal"         => true,
    ));

      $this->addColomnDef("specifiekeIndexPerformance", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("specifiekeIndexPerformance"),
    ));
      $this->addColomnDef("specifiekeIndexVorige", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "displayFormat"    => "@N",
      "description"      => vt("specifiekeIndexVorige"),
    ));

    $this->addColomnDef("datum", array(
      "type"             => "datum",
      "description"      => vt("Periode"),
      "descriptionShort" => vt("Periode"),
      "displayFormat"    => "@D{d}-{m}-{Y}",
      "widthClass"       => "w100",
    ));

    $this->addColomnDef("portefeuille", array(
      "type"             => "text",
      "formatClass"      => "attNumber",
      "description"      => vt("portefeuille"),
    ));

    
  }
}
?>