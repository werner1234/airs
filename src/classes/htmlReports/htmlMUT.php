<?php

/*
    AE-ICT
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/24 13:58:12 $
    File Versie         : $Revision: 1.10 $

    $Log: htmlMUT.php,v $
    Revision 1.10  2018/10/24 13:58:12  cvs
    call 6720

    Revision 1.9  2018/04/23 12:22:10  rm
    mutrapport toevoegen bedrag

    Revision 1.8  2018/02/09 11:09:03  cvs
    call 6270

    Revision 1.7  2017/12/13 14:29:11  cvs
    call 6349

    Revision 1.6  2017/05/10 14:16:14  rm
    Htmlrapport

    Revision 1.5  2017/05/03 13:23:45  rm
    no message

    Revision 1.4  2017/04/26 14:43:17  rm
    Html rapport

    Revision 1.3  2017/04/26 14:00:19  cvs
    toevoegen velden fonds en fondsomschrijving

    Revision 1.2  2017/04/10 14:05:19  rm
    Html rapportage

    Revision 1.1  2017/02/28 13:21:00  cvs
    no message


*/

class htmlMUT extends AE_cls_htmlColomns
{
  var $portefeuille;
  var $user;
  var $tableName      = "_htmlRapport_MUT";
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

  function htmlMUT($portefeuille)
  {
    global $USR;
    $this->user = $USR;
    $this->portefeuille = $portefeuille;

    $this->defineData();
    $this->defineSorting();
    $this->defineEndTotal();
    $this->db = new DB($this->dbId);
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
//   debug($query);
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
    $this->addSortDef("gbOmschrijving", array(
      "orderField"       => "gbOmschrijving",
      "descriptionField" => "gbOmschrijving",
      "orderBreakField"  => "gbOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));

    $this->addSortDef("Rekening", array(
      "orderField"       => "Rekening",
      "descriptionField" => "Rekening",
      "orderBreakField"  => "Rekening",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));

    $this->addSortDef("Boekdatum", array(
      "orderField"       => "Boekdatum",
      "descriptionField" => "Boekdatum",
      "orderBreakField"  => "Boekdatum",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));
  }


  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"Boekdatum",array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,"Aantal",array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'Omschrijving',array("Type"=>"varchar(80)","Null"=>false));
    $tst->changeField($this->tableName,'Grootboekrekening',array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField($this->tableName,'Afschriftnummer',array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField($this->tableName,'gbOmschrijving',array("Type"=>"varchar(40)","Null"=>false));
    $tst->changeField($this->tableName,'Debet',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'Credit',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'Rekening',array("Type"=>"varchar(28)","Null"=>false));
    $tst->changeField($this->tableName,'Valutakoers',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'Opbrengst',array("Type"=>"tinyint","Null"=>false));
    $tst->changeField($this->tableName,'Kosten',array("Type"=>"tinyint","Null"=>false));
    $tst->changeField($this->tableName,'Afdrukvolgorde',array("Type"=>"int","Null"=>false));
    $tst->changeField($this->tableName,'portefeuille',array("Type"=>"varchar(24)","Null"=>false));
    $tst->changeField($this->tableName,'fonds',array("Type"=>"varchar(60)","Null"=>false));

    $tst->changeField($this->tableName,'fondsOmschrijving',array("Type"=>"varchar(64)","Null"=>false));
    $tst->changeField($this->tableName,'Valuta',array("Type"=>"varchar(4)","Null"=>false));
    $tst->changeField($this->tableName,'Bedrag',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'rekValuta',array("Type"=>"varchar(5)","Null"=>false));
    $tst->changeField($this->tableName,'bedragVV',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'bedragEUR',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'mutationId',array("Type"=>"varchar(60)","Null"=>false));

  }


  function defineData()
  {
    global $__appvar;

    $this->addColomnDef("Boekdatum", array(
      "type"             => "date",
      "description"      => vt("Boekdatum"),
      "descriptionShort" => vt("Boekdatum"),
      "widthClass"       => "w75",
      "displayFormat"    => "@D{d}-{m}-{Y}",
      "displayFormatOrderBreakHeader"    => "@D{d}-{m}-{Y}",
      "displayFormatOrderBreakFooter"    => "@D{d}-{m}-{Y}",
      "displayFormat"       => "@D{d}-{m}-{Y}",
      "formatClass"         => "volkText",
      "sort"                => true,
    ));

    $this->addColomnDef("Aantal", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Aantal"),
      "descriptionShort"    => vt("Aantal"),
      "displayFormat"       => "@N{.6T0}",
      "headerClass"         => "headerRight",
      "widthClass"          => "w90",

    ));

    $this->addColomnDef("Omschrijving", array(
      "type"                => "text",
      "description"         => vt("Omschrijving"),
      "descriptionShort"    => vt("Omschrijving"),
      "widthClass"          => "w400",
      "sort"                => true,

    ));

    $this->addColomnDef("Grootboekrekening", array(
      "type"                => "text",
      "description"         => vt("Grootboekrekening"),
      "descriptionShort"    => vt("Grootboekrekening"),
      "sort"                => true,

    ));

    $this->addColomnDef("Afschriftnummer", array(
      "type"                => "text",
      "description"         => vt("Afschriftnummer"),
      "descriptionShort"    => vt("Afschriftnummer"),

    ));

    $this->addColomnDef("gbOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Grootboek"),
      "descriptionShort"    => vt("Grootboek"),
      'hideColumn'          => false,
      "widthClass"          => "w200",

    ));

    $this->addColomnDef("fonds", array(
      "type"                => "text",
      "description"         => vt("fonds"),
      "descriptionShort"    => vt("fonds"),
      'hideColumn'          => false

    ));
    $this->addColomnDef("mutationId", array(
      "type"                => "text",
      "description"         => vt("mutationId"),
      "descriptionShort"    => vt("mutationId"),
      'hideColumn'          => true

    ));

    $this->addColomnDef("fondsOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Fonds"),
      "widthClass"          => "w400",
      "links"               => array(
        "extraInfoVolk.php?id={id}",
      ),
      'fondsLink'           => $__appvar['baseurl'] . '/rapportFrontofficeHtml_fondsoverzicht.php?fonds={fonds}&datum_tot={stop}',
      'forceFondsLink'      => true,
      "sort"                => true,
    ));

    $this->addColomnDef("Debet", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Debet"),
      "descriptionShort"    => vt("Debet"),
      "displayFormat"       => "@N{.2b}",
      "widthClass"          => "w100",
      "sort"                => true,
    ));

    $this->addColomnDef("Credit", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Credit"),
      "descriptionShort"    => vt("Credit"),
      "displayFormat"       => "@N{.2b}",
      "widthClass"          => "w100",
      "sort"                => true,
    ));

    $this->addColomnDef("Rekening", array(
      "type"                => "text",
      "description"         => vt("Rekening"),
      "descriptionShort"    => vt("Rekening"),
      "sort"                => true,
      "widthClass"          => "w100",
    ));

    $this->addColomnDef("Valutakoers", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Valutakoers"),
    ));

    $this->addColomnDef("Opbrengst", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Opbrengst"),
    ));

    $this->addColomnDef("Kosten", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Kosten"),
    ));

    $this->addColomnDef("Afdrukvolgorde", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Afdrukvolgorde"),
    ));

    $this->addColomnDef("Bedrag", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Bedrag"),
      "descriptionShort"    => vt("Bedrag"),
      "displayFormat"       => "@N{.2b}",
      "widthClass"          => "w100",
      "sumTotal"            => true,

    ));

    $this->addColomnDef("Valuta", array(
      "type"                => "text",
      "description"         => vt("Valuta"),
      "descriptionShort"    => vt("Valuta"),
      "sort"                => true,
      "widthClass"          => "w100",
    ));

    $this->addColomnDef("rekValuta", array(
      "type"                => "text",
      "description"         => vt("rekValuta"),
      "descriptionShort"    => vt("rekValuta"),
      "sort"                => true,
      "widthClass"          => "w100",
    ));


    $this->addColomnDef("bedragVV", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Bedrag VV"),
      "descriptionShort"    => vt("Bedrag VV"),
      "displayFormat"       => "@N{.2b}",
      "widthClass"          => "w100",
      "sumTotal"            => true,

    ));

    $this->addColomnDef("bedragEUR", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Bedrag EUR"),
      "descriptionShort"    => vt("Bedrag EUR"),
      "displayFormat"       => "@N{.2b}",
      "widthClass"          => "w100",
      "sumTotal"            => true,

    ));


  }
}

?>