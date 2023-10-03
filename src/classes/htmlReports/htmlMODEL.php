<?php

/*
    AE-ICT
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/08/30 14:59:54 $
    File Versie         : $Revision: 1.10 $

    $Log: htmlMODEL.php,v $
    Revision 1.10  2019/08/30 14:59:54  rm
    consolidatie

    Revision 1.9  2018/04/20 14:47:00  rm
    6839  Model: Totalen onder kolommen

    Revision 1.8  2018/04/04 14:22:56  rm
    6019

    Revision 1.7  2017/10/13 12:51:41  rm
    Html rapport

    Revision 1.6  2017/09/15 15:00:12  rm
    model rapport

    Revision 1.5  2017/09/15 11:39:18  cvs
    call 6019

    Revision 1.4  2017/09/12 15:04:22  rm
    Html rapport

    Revision 1.3  2017/09/06 12:56:03  cvs
    CALL 6019

    Revision 1.2  2017/08/31 09:40:32  rm
    Html rapportage

    Revision 1.1  2017/08/09 14:19:02  cvs
    call 6019

    Revision 1.23  2017/07/07 14:28:31  rm
    Toevoegen van velden

    Revision 1.22  2017/06/14 13:34:18  rm
    orders opnieuw inleggen en inleggen vanuit rapportage

    Revision 1.21  2017/05/11 12:15:06  rm
    Htmlrapport

    Revision 1.20  2017/05/10 14:16:14  rm
    Htmlrapport

    Revision 1.19  2017/05/08 13:24:42  rm
    weghalen variable namen

    Revision 1.18  2017/02/01 15:13:03  rm
    no message

    Revision 1.17  2017/01/11 15:07:10  rm
    Html rapportage

    Revision 1.16  2017/01/11 14:23:24  rm
    Html rapportage

    Revision 1.15  2016/12/19 07:44:15  rm
    Html rapport

    Revision 1.14  2016/12/02 09:05:54  rm
    Html rapportage

    Revision 1.13  2016/10/31 11:49:20  cvs
    call 3856

    Revision 1.12  2016/10/21 06:23:42  rm
    Html rapport

    Revision 1.11  2016/10/18 09:08:44  cvs
    update cvs 20161018

    Revision 1.10  2016/10/17 09:33:15  rm
    htmlrapport

    Revision 1.9  2016/09/28 14:08:42  rm
    html rapport

    Revision 1.8  2016/09/07 12:00:46  rm
    html rapportage

    Revision 1.7  2016/08/31 13:04:39  cvs
    call 5213

    Revision 1.6  2016/08/31 10:49:35  cvs
    call 4848: derde bestand Kasbankl



*/

class htmlMODEL extends AE_cls_htmlColomns
{
  var $portefeuille;
  var $user;
  var $tableName      = "_htmlRapport_MODEL";
  var $properties     = array(
    "type"             => "text",         // soort veld
    "description"      => "",             // kolom omschrijving
    "descriptionShort" => "",             // kolom omschrijving kort
    "widthClass"       => "w10",         // class tbv breedte kolom
    "displayFormat"    => "@S{0}",        // printf formaat
    "formatClass"      => "volkText",     // class tbv opmaak
    "negativeClass"    => "volkNegative", // class tbv negatieve waarde
    "headerClass"      => "headerLeft",   // header class alignment
    "sumTotal"         => false,          // true als kolom totaliseerbaar is
    "sumClass"         => "volkSubTotal", // class tbv opmaak sum waarde
    "sumFormat"        => "@N{.2}",         // printf formaat sum waarde
    "sumTotalFormat"   => "@N{.2}",         // printf formaat toataal sum waarde
    "position"         => 10,             // kolom positie in matrix
    "sort"             => false,          // kolom is sorteerbaar
    "hideColumn"       => false,          //Kolom kan niet worden getoond
    "visible"          => true,
    "hideEndTotal"     => false,
    "value"            => null,
    'clickable'        => '',

    "links" => array(),
    "fixed" => false           // Bij true kan het veld niet verborgen of gesorteerd worden
  );
  var $sortProperties = array(
    "orderField"       => "",
    "descriptionField" => "",
    "orderIncremental" => true,
    "disable"          => false,
    "prio"             => 100,
  );

  var $endTotal;

  var $data = array();
  var $db;

  function htmlMODEL($portefeuille)
  {
    global $USR;
    $this->user = $USR;
    $this->portefeuille = $portefeuille;
    $this->defineData();
    $this->defineSorting();
    $this->defineEndTotal();
    $this->db = new DB($this->dbId);
  }

  function getStamgegevens()
  {
    $query = "SELECT stamgegevens FROM `" . $this->tableName . "` WHERE portefeuille = '" . $this->portefeuille . "'";
    $record = $this->db->lookupRecordByQuery($query);
    return unserialize($record["stamgegevens"]);
  }

  function clearTable()
  {
    // verwijder historie ouder dan vandaag
    $query = "DELETE FROM `" . $this->tableName . "` WHERE `add_date` <= '" . date("Y-m-d", mktime() - 86400) . " 23:59:59' ";

    $this->db->executeQuery($query);
    // verwijder historie van portefeuille
    $query = "DELETE FROM `" . $this->tableName . "` WHERE `add_user` = '" . $this->user . "' AND portefeuille = '" . $this->portefeuille . "'";
    $this->db->executeQuery($query);
  }

  function addRecord($data)
  {
    $query = "INSERT INTO `" . $this->tableName . "` SET ";
    $query .= "  add_date = NOW() ";
    $query .= "  ,change_date = NOW() ";
    $query .= "  ,add_user = '" . $this->user . "' ";
    $query .= "  ,change_user = '" . $this->user . "' ";
    foreach ($data as $k => $v)
    {
      $query .= "\n  , `$k` = '" . addslashes($v) . "' ";
    }

    $this->db->executeQuery($query);
  }

  function defineEndTotal()
  {
    $this->endTotal = array(
      "negativeClass" => "endTotalNegative", // class tbv negatieve waarde
      "class"         => "endTotal",         // class tbv opmaak sum waarde
      "format"        => "@N{.0}",           // printf formaat sum waarde
    );
  }

  function defineSorting()
  {
    $this->addSortDef("valuta", array(
      "orderField"       => "valutaVolgorde, valutaOmschrijving",
      "descriptionField" => "valutaOmschrijving",
      "orderBreakField"  => "valutaOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));
    $this->addSortDef("beleggingscategorie", array(
      "orderField"       => "beleggingscategorieVolgorde, beleggingscategorieOmschrijving",
      "descriptionField" => "beleggingscategorieOmschrijving",
      "orderBreakField"  => "beleggingscategorieOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "valutaHeader",
      "prio"             => 2,
    ));
    $this->addSortDef("beleggingssector", array(
      "orderField"       => "beleggingssectorVolgorde, beleggingssectorOmschrijving",
      "descriptionField" => "beleggingssectorOmschrijving",
      "orderBreakField"  => "beleggingssectorOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTwo",
      "prio"             => 2,
    ));
    $this->addSortDef("hoofdcategorie", array(
      "orderField"       => "hoofdcategorieVolgorde",
      "descriptionField" => "hoofdcategorieOmschrijving",
      "orderBreakField"  => "hoofdcategorieOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "prio"             => 3,
    ));
//    $this->addSortDef("afmCategorie", array(
//      "orderField"       => "afmCategorieVolgorde, afmCategorieOmschrijving",
//      "orderBreakField"  => "afmCategorieOmschrijving",
//      "descriptionField" => "afmCategorieOmschrijving",
//      "orderIncremental" => true,
//      "disable"          => false,
//      "prio"             => 3,
//      "headerClass"      => "headerOne",
//    ));
    $this->addSortDef("Regio", array(
      "orderField"       => "regioVolgorde",
      "descriptionField" => "regioOmschrijving",
      "orderBreakField"  => "regioOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "prio"             => 3,
    ));


  }

  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);

    $tst->changeField($this->tableName,"datum",array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,'type',array("Type"=>"varchar(5)","Null"=>false));
    $tst->changeField($this->tableName,'fonds',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'fondsOmschrijving',array("Type"=>"varchar(64)","Null"=>false));
    $tst->changeField($this->tableName,'modelPercentage',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'werkelijkPercentage',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'afwijkingPercentage',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'afwijkingEur',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'kopen',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'verkopen',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'waardeModel',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'koersLokaal',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'geschatOrderbedrag',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'modelWaarde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'huidigeWaarde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'portefeuille',array("Type"=>"varchar(24)","Null"=>false));
    $tst->changeField($this->tableName,'ISINCode',array("Type"=>"varchar(24)","Null"=>false));
    $tst->changeField($this->tableName,'valuta',array("Type"=>"varchar(4)","Null"=>false));
    $tst->changeField($this->tableName,'stamgegevens',array("Type"=>"text","Null"=>false));
    ////////////////////////
    $tst->changeField($this->tableName,'hoofdcategorie',array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField($this->tableName,'hoofdsector',array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField($this->tableName,'Regio',array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField($this->tableName,'beleggingscategorie',array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField($this->tableName,'beleggingssector',array("Type"=>"varchar(30)","Null"=>false));

    $tst->changeField($this->tableName,'hoofdcategorieVolgorde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'hoofdcategorieOmschrijving',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'hoofdsectorVolgorde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'hoofdsectorOmschrijving',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'valutaVolgorde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'valutaOmschrijving',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'regioVolgorde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'regioOmschrijving',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'beleggingscategorieVolgorde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'beleggingscategorieOmschrijving',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'beleggingssectorVolgorde',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'beleggingssectorOmschrijving',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'consolidatie',array("Type"=>"int(3)","Null"=>false));

  }


  function defineData()
  {
    global $__appvar;

    $this->addColomnDef("type", array(
      "type"             => "text",
      "description"      => vt("soortFonds"),
      "descriptionShort" => vt("soortFonds"),
      "widthClass"       => "w10",
      "formatClass"      => "volkText",
      "position"         => 0,
      "sort"             => false,
      'hideColumn'       => true,
      'visible'          => false
    ));


   $this->addColomnDef("datum", array(
      "type"        => "date",
      "description" => vt("datum"),
    ));

   $this->addColomnDef("fonds", array(
      "type"        => "text",
      "formatClass" => "volkText",
      "description" => vt("Fonds"),
      "widthClass"  => "w100",
    ));

   $this->addColomnDef("fondsOmschrijving", array(
      "type"                  => "text",
      "formatClass"           => "volkText",
      "description"           => vt("Fondsomschrijving"),
      "descriptionShort"      => vt("Fondsomschrijving"),
      "widthClass"            => "w400",
      "sort"                  => true,
      'showOrderLink'         => true,
      'showOrderCheckbox'     => true,
      "links"                 => array(
        "extraInfoVolk.php?id={id}",
      ),
    ));

   $this->addColomnDef("portefeuille", array(
      "type"        => "text",
      "formatClass" => "volkText",
      "description" => vt("portefeuille"),
    ));
$this->addColomnDef("ISINCode", array(
      "type"                => "text",
      "formatClass"         => "volkText",
      "description"         => vt("ISINCode"),
      "visible"             => false,
    ));

    $this->addColomnDef("modelPercentage", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Model Percentage"),
      "descriptionShort"    => vt("Model Percentage"),
      "displayFormat"       => "@N{.2}%",
      "sumFormat"           => "@N{.2}%",
      "sumTotalFormat"      => "@N{.2}%",
      "sort"                => true,
    ));

    $this->addColomnDef("werkelijkPercentage", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("Werkelijk Percentage"),
      "descriptionShort" => vt("Werkelijk Percentage"),
      "displayFormat"    => "@N{.2}%",
      "sumFormat"        => "@N{.2}%",
      "sumTotalFormat"   => "@N{.2}%",
      "sort"             => true,
    ));

    $this->addColomnDef("afwijkingPercentage", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "description"       => vt("Afwijking Percentage"),
      "descriptionShort"       => vt("Afwijking Percentage"),
      "displayFormat"     => "@N{.2}%",
      "sumFormat"        => "@N{.2}%",
      "sumTotalFormat"   => "@N{.2}%",
      "sort"              => true,
    ));

    $this->addColomnDef("afwijkingEur", array(
      "type"          => "number",
      "formatClass"   => "volkNumber",
      "description"   => vt("Afwijking in Euro"),
      "displayFormat" => "@N{.2}",
      "sumFormat"        => "@N{.2}",
      "sumTotalFormat"   => "@N{.2}",
      "widthClass"  => "w100",
    ));

    $this->addColomnDef("kopen", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("Kopen"),
      "displayFormat" => "@N{.0}",
      "widthClass"  => "w80",
    ));

    $this->addColomnDef("verkopen", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("Verkopen"),
      "displayFormat" => "@N{.0}",
      "widthClass"  => "w80",
    ));

    $this->addColomnDef("waardeModel", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("Waarde volgens model"),
      "displayFormat" => "@N{.2}",
      "widthClass"  => "w100",
    ));

    $this->addColomnDef("koersLokaal", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Koers in locale valuta"),
      "displayFormat"       => "@N{.2}",
      "widthClass"          => "w100",
    ));

    $this->addColomnDef("geschatOrderbedrag", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Geschat orderbedrag"),
      "descriptionShort"    => vt("Geschat orderbedrag"),
      "displayFormat"       => "@N{.2}",
      "widthClass"          => "w100",
      "sort"                => true,

    ));

    $this->addColomnDef("modelWaarde", array(
      "type"             => "text",
      "description"      => vt("Model waarde"),
      "displayFormat" => "@N{.2}",

    ));

    $this->addColomnDef("huidigeWaarde", array(
      "type"             => "text",
      "description"      => vt("Huidige Waarde"),
      "displayFormat" => "@N{.2}",

    ));
    $this->addColomnDef("hoofdcategorie", array(
      "type"             => "text",
      "description"      => vt("hoofdcategorie"),


    ));
    $this->addColomnDef("hoofdsector", array(
      "type"             => "text",
      "description"      => vt("hoofdsector"),


    ));
    $this->addColomnDef("Regio", array(
      "type"             => "text",
      "description"      => vt("Regio"),


    ));
    $this->addColomnDef("beleggingscategorie", array(
      "type"             => "text",
      "description"      => vt("beleggingscategorie"),


    ));
    $this->addColomnDef("beleggingssector", array(
      "type"             => "text",
      "description"      => vt("beleggingssector"),


    ));







    $this->addColomnDef("beleggingscategorie", array(
      "type"        => "text",
      "description" => vt("beleggingscategorie"),
      'visible'     => false
    ));
    $this->addColomnDef("beleggingscategorieVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("BeleggingscategorieVolgorde"),
    ));
    $this->addColomnDef("beleggingscategorieOmschrijving", array(
      "type"        => "text",
      "description"      => vt("Beleggingscategorieën"),
      "descriptionShort" => vt("Beleggingscategorieën"),
      'visible'     => false
    ));

    $this->addColomnDef("valutaVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("RegioOmschrijving"),
    ));
    $this->addColomnDef("valutaOmschrijving", array(
      "type"        => "text",
      "description" => vt("ValutaOmschrijving"),
    ));
    $this->addColomnDef("valuta", array(
      "type"          => "text",
      "description"   => vt("Valuta"),
      "displayFormat" => "@S{3}",
      "widthClass"    => "w50",
      "formatClass"   => "ac volkText",
      "visible"     => false,
    ));

    $this->addColomnDef("beleggingssector", array(
      "type"        => "text",
      "description" => vt("beleggingssector"),
    ));

    $this->addColomnDef("beleggingssectorVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("BeleggingssectorVolgorde"),
    ));

    $this->addColomnDef("beleggingssectorOmschrijving", array(
      "type"        => "text",
      "description"      => vt("Beleggingssector"),
      "descriptionShort" => vt("Beleggingssector"),
      'visible'     => false
    ));

    $this->addColomnDef("hoofdcategorie", array(
      "type"        => "text",
      "description" => vt("Hoofdcategorie"),
    ));

    $this->addColomnDef("hoofdcategorieOmschrijving", array(
      "type"        => "text",
      "description" => vt("HoofdcategorieOmschrijving"),
      "description"      => vt("Hoofdcategorieën"),
      "descriptionShort" => vt("Hoofdcategorieën"),
      'visible'     => false
    ));
    $this->addColomnDef("hoofdcategorieVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("HoofdcategorieVolgorde"),
    ));
//
//    $this->addColomnDef("afmCategorie", array(
//      "type"        => "text",
//      "description" => "afmCategorie",
//    ));
//    $this->addColomnDef("afmCategorieOmschrijving", array(
//      "type"        => "text",
//      "description" => "AfmCategorieOmschrijving",
//      "description"      => "Afm categorieën",
//      "descriptionShort" => "Afm categorieën",
//      'visible'     => false
//    ));

    $this->addColomnDef("Regio", array(
      "type"        => "text",
      "description" => vt("Regio"),
    ));

    $this->addColomnDef("regioVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("RegioVolgorde"),
    ));
    $this->addColomnDef("regioOmschrijving", array(
      "type"        => "text",
      "description" => vt("Regio"),
      "descriptionShort" => vt("Regio"),
      'visible'     => false
    ));








  }
}

?>