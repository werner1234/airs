<?php

/*
    AE-ICT
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2017/05/11 12:15:06 $
    File Versie         : $Revision: 1.7 $

    $Log: htmlTRANS.php,v $
    Revision 1.7  2017/05/11 12:15:06  rm
    Htmlrapport

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

class htmlTRANS extends AE_cls_htmlColomns
{
  var $portefeuille;
  var $user;
  var $tableName      = "_htmlRapport_TRANS";
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

  function htmlTRANS($portefeuille)
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
    //debug($query);
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
    $this->addSortDef("transactietype", array(
      "orderField"       => "transactietype",
      "descriptionField" => "transactietype",
      "orderBreakField"  => "transactietype",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));
$this->addSortDef("datum", array(
      "orderField"       => "datum",
      "descriptionField" => "datum",
      "orderBreakField"  => "datum",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));
$this->addSortDef("fonds", array(
      "orderField"       => "fonds",
      "descriptionField" => "fonds",
      "orderBreakField"  => "fonds",
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

    $tst->changeField($this->tableName,"datum",array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,'transactietype',array("Type"=>"varchar(5)","Null"=>false));
    $tst->changeField($this->tableName,'aantal',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'fonds',array("Type"=>"varchar(60)","Null"=>false));
    $tst->changeField($this->tableName,'fondsOmschrijving',array("Type"=>"varchar(64)","Null"=>false));
    $tst->changeField($this->tableName,'aankoopKoersValuta',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'aankoopWaardeValuta',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'aankoopWaardeEur',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'verkoopKoersValuta',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'verkoopWaardeValuta',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'verkoopWaardeEur',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'historischeKostprijsEur',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'resultaatEur',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'resultaatvoorgaandEur',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'resultaatgedurendEur',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'resultaatPercent',array("Type"=>"double","Null"=>false));
    $tst->changeField($this->tableName,'portefeuille',array("Type"=>"varchar(24)","Null"=>false));
  }


  function defineData()
  {
    global $__appvar;

    $this->addColomnDef("datum", array(
      "type"              => "date",
      "description"       => vt("Datum"),
      "descriptionShort" => vt("Datum"),
      "displayFormat"     => "@D{d}-{m}-{Y}",
      "displayFormatOrderBreakHeader"    => "@D{d}-{m}-{Y}",
      "displayFormatOrderBreakFooter"    => "@D{d}-{m}-{Y}",
      "widthClass"        => "w10",
      "sort"             => true,
    ));

    $this->addColomnDef("transactietype", array(
      "type"              => "text",
      "description"       => vt("Transactietype"),
      "descriptionShort"  => vt("TT"),
      "widthClass"        => "w1",
      "sort"              => true,
    ));

    $this->addColomnDef("aantal", array(
      "type"                => "number",
      "formatClass"         => "text-right",
      "description"         => vt("Aantal"),
      "descriptionShort"    => vt("Aantal"),
      "displayFormat"       => "@N{.4T0} ",
      "sort"                => true,
    ));

    $this->addColomnDef("fondsOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Fonds"),
      "descriptionShort"    => vt("Fonds"),
      "widthClass"          => "w75",
      "links"               => array(
        "extraInfoVolk.php?id={id}",
      ),
      'fondsLink'           => $__appvar['baseurl'] . '/rapportFrontofficeHtml_fondsoverzicht.php?fonds={fonds}&datum_tot={stop}',
      'forceFondsLink'      => true,
      "sort"                => true,
    ));


    $this->addColomnDef("fonds", array(
      "type"        => "text",
      "description" => vt("fonds"),
    ));


    $this->addColomnDef("aankoopKoersValuta", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "descriptionShort"  => vt("FondsKoers"),
      "description"       => vt("Aankoopkoers in fondsvaluta"),
      "displayFormat"     => "@n{.2b}",
      "widthClass"        => "w15",
      "sort"              => true,
    ));

    $this->addColomnDef("aankoopWaardeValuta", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "descriptionShort"  => vt("Waarde"),
      "description"       => vt("Aankoopwaarde in fondsvaluta"),
      "displayFormat"     => "@n{.2b}",
      "widthClass"        => "w15",
      "sort"              => true,
    ));

    $this->addColomnDef("aankoopWaardeEur", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "descriptionShort"    => vtb("Waarde %s EUR", array('<br />')),
      "description"         => vt("Aankoopwaarde in Euro"),
      "displayFormat"       => "@n{.2b}",
      "widthClass"          => "w90",
      "sumTotal"            => true,
      "sort"                => true,
    ));

    $this->addColomnDef("verkoopKoersValuta", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "descriptionShort"    => vt("Koers"),
      "description"         => vt("Verkoopkoers in fondsvaluta"),
      "displayFormat"       => "@n{.2b}",
      "widthClass"          => "w75",
      "sort"                => true,
    ));

    $this->addColomnDef("verkoopWaardeValuta", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "descriptionShort"  => vt("Waarde"),
      "description"       => vt("Verkoopwaarde in fondsvaluta"),
      "displayFormat"     => "@n{.2b}",
      "widthClass"        => "w10",
      "sort"              => true,
    ));

    $this->addColomnDef("verkoopWaardeEur", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "descriptionShort"  => vtb("Waarde %s EUR", array('<br />')),
      "description"       => vt("Verkoopwaarde in Euro"),
      "displayFormat"     => "@n{.2b}",
      "widthClass"        => "w80",
      "sumTotal"          => true,
      "sort"              => true,
    ));

    $this->addColomnDef("historischeKostprijsEur", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "descriptionShort"  => vt("Kostprijs"),
      "description"       => vt("Historische kostprijs in Euro"),
      "displayFormat"     => "@n{.2b}",
      "widthClass"        => "w75",
      "sort"              => true,
    ));

    $this->addColomnDef("resultaatEur", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "description"       => vt("resultaatEur"),
      "descriptionShort"  => vt("resultaatEur"),
      "widthClass"        => "w10",
      "sort"              => false,
    ));

    $this->addColomnDef("resultaatvoorgaandEur", array(
      "type"                  => "number",
      "formatClass"           => "volkNumber",
      "descriptionShort"      => vtb("Res. %S voorg. jr.", array ('<br />')),
      "description"           => vt("Resultaat voorafgaand aan huidig jaar"),
      "displayFormat"         => "@n{.2b}",
      "widthClass"            => "w75",
      "sumTotal"              => true,
      "sort"                  => true,
    ));

    $this->addColomnDef("resultaatgedurendEur", array(
      "type"                  => "number",
      "formatClass"           => "volkNumber",
      "descriptionShort"      => vtb("Res. %S YtD", array('<br />')),
      "description"           => vt("Resultaat huidig jaar"),
      "displayFormat"         => "@n{.2b}",
      "widthClass"            => "w50",
      "sumTotal"              => true,
      "sort"                  => true,
    ));

    $this->addColomnDef("resultaatPercent", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "descriptionShort"  => vtb("Res. %s in %", array('<br />')),
      "description"       => vt("Resultaat in procenten"),
      "displayFormat"     => "@n{.1b}%",
      "widthClass"        => "w50",
      "sort"              => true,
    ));

    $this->addColomnDef("portefeuille", array(
      "type"              => "text",
      "description"       => vt("portefeuille"),
      "widthClass"        => "w20",
      "sort"              => false,
    ));


  }
}

?>