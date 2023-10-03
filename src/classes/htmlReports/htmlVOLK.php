<?php

/*
    AE-ICT
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/09/04 13:34:41 $
    File Versie         : $Revision: 1.29 $

    $Log: htmlVOLK.php,v $
    Revision 1.29  2019/09/04 13:34:41  rm
    7399

    Revision 1.28  2018/11/06 14:58:17  rm
    6775 Orders via HTML (nominaal + beurs)

    Revision 1.27  2017/12/08 18:23:43  rm
    no message

    Revision 1.26  2017/12/04 14:55:54  rm
    Html rapport

    Revision 1.25  2017/12/01 14:27:43  rm
    6371 HTML VOLK: Kolom KOersdatum toevoegen

    Revision 1.24  2017/08/15 10:30:58  rm
    Html rapportage

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

class htmlVOLK extends AE_cls_htmlColomns
{
  var $portefeuille;
  var $user;
  var $tableName      = "_htmlRapport_VOLK";
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

  function htmlVOLK($portefeuille = '')
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
    $this->addSortDef("valuta", array(
      "orderField"       => "valutaVolgorde, valutaOmschrijving",
      "descriptionField" => "valuta",
      "orderBreakField"  => "valuta",
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
    $this->addSortDef("afmCategorie", array(
      "orderField"       => "afmCategorieVolgorde, afmCategorieOmschrijving",
      "orderBreakField"  => "afmCategorieOmschrijving",
      "descriptionField" => "afmCategorieOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "prio"             => 3,
      "headerClass"      => "headerOne",
    ));
    $this->addSortDef("Regio", array(
      "orderField"       => "regioVolgorde",
      "descriptionField" => "regioOmschrijving",
      "orderBreakField"  => "regioOmschrijving",
      "orderIncremental" => true,
      "disable"          => false,
      "prio"             => 3,
    ));

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
      "negativeClass"    => "",
      "sumTotal"         => false,
      "sumClass"         => "",
      "sumFormat"        => "@N{.0B}",
      "position"         => 0,
      "sort"             => false,
      'hideColumn'       => true,
      'visible'          => false
    ));

    $this->addColomnDef("totaalAantal", array(
      "type"             => "number",
      "formatClass"      => "volkNumber",
      "description"      => vt("Aantal"),
      "descriptionShort" => vt("Aantal"),
      "displayFormat"    => "@N{.6T0}",
      "headerClass"      => "headerRight",
      "sort"             => true,
      "widthClass"       => "w90",

    ));

    $this->addColomnDef("historischeWaarde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("historische waarde"),
    ));

    $this->addColomnDef("historischeValutakoers", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("historische valutakoers"),
    ));

    $this->addColomnDef("historischeRapportageValutakoers", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("historische Rapportage Valutakoers"),
    ));

    $this->addColomnDef("beginwaardeLopendeJaar", array(
      "type"          => "number",
      "formatClass"   => "volkNumber",
      "description"   => vt("Kostprijs lopend jaar"),
      "displayFormat" => "@N{.2}",        // printf formaat
      "headerClass"   => "headerRight",
      "widthClass"    => "w90",
    ));

    $this->addColomnDef("fondsEenheid", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("Fondseenheid"),
      "visible"     => false,
      "widthClass"  => "w80",
    ));

    $this->addColomnDef("beginwaardeValutaLopendeJaar", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("beginwaarde Valuta LopendeJaar"),
    ));

    $this->addColomnDef("renteBerekenen", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("rente Berekenen"),
    ));

    $this->addColomnDef("voorgaandejarenActief", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("voorgaandejaren Actief"),
    ));

    $this->addColomnDef("fondsOmschrijving", array(
      "type"                    => "text",
      "description"             => vt("Fondsomschrijving"),
      "descriptionShort"        => vt("Fondsomschrijving"),
      "widthClass"              => "w400",
      "sort"                    => true,
      "links"            => array(
        "extraInfoVolk.php?id={id}",
      ),
      'fondsLink'                => $__appvar['baseurl'] . '/rapportFrontofficeHtml_fondsoverzicht.php?fonds={fonds}&datum_tot={stop}',
//      'clickable' => $__appvar['baseurl'] . '/rapportFrontofficeHtml_fondsoverzicht.php?vb={Vermogensbeheerder}&fonds={fonds}'
    'showOrderLink'              => true
    ));

    $this->addColomnDef("valuta", array(
      "type"                  => "text",
      "description"           => vt("Valuta"),
      "descriptionShort"      => vt("Valuta"),
      "sort"                  => true,
      "displayFormat"         => "@S{3}",
      "widthClass"            => "w50",
      "formatClass"           => "ac volkText",
    ));

    $this->addColomnDef("eersteRentedatum", array(
      "type"        => "date",
      "description" => vt("eerste Rentedatum"),
    ));

    $this->addColomnDef("rentedatum", array(
      "type"        => "date",
      "description" => vt("rentedatum"),
    ));

    $this->addColomnDef("renteperiode", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("rente periode"),
    ));

    $this->addColomnDef("actueleValuta", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("actuele Valuta"),
    ));

    /** fonds koers */
    $this->addColomnDef("actueleFonds", array(
      "type"          => "number",
      "formatClass"   => "volkNumber",
      "description"   => vt("Huidige koers"),
      "displayFormat" => "@N{.6T2}",
      "widthClass"    => "w90",
    ));

    $this->addColomnDef("koersDatum", array(
      "type"                                => "date",
      "description"                         => vt("Koersdatum"),
      "descriptionShort"                    => vt("Koersdatum"),
      'visible'                             => false,
      "displayFormat"                       => "@D{d}-{m}-{Y}",
      "displayFormatOrderBreakHeader"       => "@D{d}-{m}-{Y}",
      "displayFormatOrderBreakFooter"       => "@D{d}-{m}-{Y}",
      "widthClass"                          => "w10",
      "sort"                                => true,
    ));

    $this->addColomnDef("Bewaarder", array(
      "type"              => "text",
      "description"       => vt("Bewaarder"),
    ));

    $this->addColomnDef("beginPortefeuilleWaardeInValuta", array(
      "type"              => "number",
      "formatClass"       => "volkNumber",
      "description"       => vt("Beginwaarde lopend jaar"),
      "displayFormat"     => "@N{.0}",        // printf formaat
      "widthClass"        => "w90",
    ));

    $this->addColomnDef("beginPortefeuilleWaardeEuro", array(
      "type"           => "number",
      "formatClass"    => "volkNumber",
      "displayFormat"  => "@N{.0}",
      "sumTotal"       => true,
      "hideEndTotal"   => true,
      "sumFormat"      => "@N{.0}",
      "sumTotalFormat" => "@N{.0}",
      "description"    => vt('Beginwaarde lopend jaar') . " {RapportageValuta}",
//      "description"    => "Beginwaarde lopend jaar",
      "widthClass"     => "w125",
    ));

    $this->addColomnDef("actuelePortefeuilleWaardeInValuta", array(
      "type"             => "number",
      "formatClass"      => "volkNumber",
      "description"      => vt("Huidige waarde"),
      "descriptionShort" => vt("Huidige waarde"),
      "formatClass"      => "volkNumber",
      "displayFormat"    => "@N{.0}",
      "widthClass"       => "w100",
    ));

    $this->addColomnDef("actuelePortefeuilleWaardeEuro", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "displayFormat"       => "@N{.0}",
//      "description"      => "Huidige waarde ",
      "description"         => vt('Huidige waarde') . " <br /> {RapportageValuta}",
//      "descriptionShort" => "Huidige waarde",
      "descriptionShort"    => vt('Huidige waarde') . " <br /> {RapportageValuta}",
      "sumTotal"            => true,
      "sumFormat"           => "@N{.0}",
      "sumTotalFormat"      => "@N{.0}",
      "sort"                => true,
      "widthClass"          => "w125",
    ));

    $this->addColomnDef("fonds", array(
      "type"        => "text",
      "description" => vt("fonds"),
      "hideColumn"  => true,
      'visible'     => false
    ));

    $this->addColomnDef("beleggingssector", array(
      "type"        => "text",
      "description" => vt("beleggingssector"),
    ));

    $this->addColomnDef("beleggingscategorie", array(
      "type"        => "text",
      "description" => vt("beleggingscategorie"),
      'visible'     => false
    ));

    $this->addColomnDef("lossingsdatum", array(
      "type"        => "date",
      "description" => vt("lossingsdatum"),
    ));

    $this->addColomnDef("Regio", array(
      "type"        => "text",
      "description" => "Regio",
    ));

    $this->addColomnDef("AttributieCategorie", array(
      "type"            => "text",
      "description"     => vt("AttributieCategorie"),
    ));

    $this->addColomnDef("afmCategorie", array(
      "type"            => "text",
      "description"     => vt("afmCategorie"),
    ));

    $this->addColomnDef("beleggingscategorieOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Beleggingscategorieën"),
      "descriptionShort"    => vt("Beleggingscategorieën"),
      "sort"                => true,
      'visible'             => false
    ));

    $this->addColomnDef("beleggingssectorOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Beleggingssector"),
      "descriptionShort"    => vt("Beleggingssector"),
      "sort"                => true,
      'visible'             => false
    ));

    $this->addColomnDef("hoofdcategorie", array(
      "type"            => "text",
      "description"     => vt("Hoofdcategorie"),
    ));

    $this->addColomnDef("hoofdsector", array(
      "type"            => "text",
      "description"     => vt("Hoofdsector"),
    ));

    $this->addColomnDef("hoofdcategorieOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Hoofdcategorieën"),
      "descriptionShort"    => vt("Hoofdcategorieën"),
      'visible'             => false
    ));

    $this->addColomnDef("hoofdsectorOmschrijving", array(
      "type"            => "text",
      "description"     => vt("HoofdsectorOmschrijving"),
    ));

    $this->addColomnDef("attributieCategorieOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Attributie categorieën"),
      "descriptionShort"    => vt("Attributie categorieën"),
      "sort"                => true,
      'visible'             => false
    ));

    $this->addColomnDef("afmCategorieOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Afm categorieën"),
      "descriptionShort"    => vt("Afm categorieën"),
      'visible'             => false
    ));

    $this->addColomnDef("regioOmschrijving", array(
      "type"                => "text",
      "description"         => vt("Regio"),
      "descriptionShort"    => vt("Regio"),
      "sort"                => true,
      'visible'             => false
    ));

    $this->addColomnDef("valutaOmschrijving", array(
      "type"        => "text",
      "description" => vt("ValutaOmschrijving"),
    ));

    $this->addColomnDef("valutaVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("RegioOmschrijving"),
    ));

    $this->addColomnDef("hoofdcategorieVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("HoofdcategorieVolgorde"),
    ));

    $this->addColomnDef("hoofdsectorVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("HoofdsectorVolgorde"),
    ));

    $this->addColomnDef("beleggingssectorVolgorde", array(
      "type"        => "number",
      "formatClass" => "volkNumber",
      "description" => vt("BeleggingssectorVolgorde"),
    ));

    $this->addColomnDef("beleggingscategorieVolgorde", array(
      "type"            => "number",
      "formatClass"     => "volkNumber",
      "description"     => vt("BeleggingscategorieVolgorde"),
     
    ));

    $this->addColomnDef("regioVolgorde", array(
      "type"            => "number",
      "formatClass"     => "volkNumber",
      "description"     => vt("RegioVolgorde"),
    ));

    $this->addColomnDef("attributieCategorieVolgorde", array(
      "type"            => "number",
      "formatClass"     => "volkNumber",
      "description"     => vt("AttributieCategorieVolgorde"),
    ));

    $this->addColomnDef("aandeelOpTotaleWaarde", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "displayFormat"       => "@N{.1} %",
      "description"         => vt("Weging"),
      "descriptionShort"    => vt("Weging"),
      "sort"                => true,
      "sumTotal"            => true,
      "sumFormat"           => "@N{.1B} %",
      "sumTotalFormat"      => "@N{.1B} %",
      "widthClass"          => "w50",
    ));

    $this->addColomnDef("fondsResultaat", array(
      "type"             => "number",
      "formatClass"      => "volkNumber",
      "description"      => vt("Fondsresultaat exclusief directe opbrengsten"),
      "descriptionShort" => vt("Fondsresultaat"),
      "sort"             => true,
      "sumTotal"         => true,
      "sumFormat"        => "@N{.0}",
      "sumTotalFormat"   => "@N{.0}",
      "displayFormat"    => "@N{.0}",
      "widthClass"       => "w90",

    ));

    $this->addColomnDef("valutaResultaat", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "displayFormat"       => "@N{.0}",
      "description"         => vt("Valutaresultaat"),
      "descriptionShort"    => vt("Valutaresultaat"),
      "sort"                => true,
      "sumFormat"           => "@N{.0}",
      "sumTotalFormat"      => "@N{.0}",
      "sumTotal"            => true,
      "widthClass"          => "w90",
    ));

    $this->addColomnDef("resultaatInProcent", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "displayFormat"       => "@N{.1}%",
      "description"         => vt("Resultaat in %"),
      "descriptionShort"    => vt("Resultaat in %"),
      "sort"                => true,
      "widthClass"          => "w90",
    ));

    $this->addColomnDef("rekening", array(
      "type"                => "text",
      "description"         => vt("Rekeningnummer"),
    ));
    $this->addColomnDef("add_date", array(
      "type"                => "date",
      "description"         => vt("toegevoegd"),
      "displayFormat"       => "@D {d}-{m}-{Y} {H}:{i}",
    ));
    $this->addColomnDef("id", array(
      "type"                => "number",
      "formatClass"         => "volkNumber",
      "description"         => vt("Record id"),
    ));
    $this->addColomnDef("portefeuille", array(
      "type"                => "text",
      "description"         => vt("Portefeuille"),
    ));
    $this->addColomnDef("rapportDatum", array(
      "type"                => "date",
      "description"         => vt("Rapportage datum"),
    ));

    $this->addColomnDef("ISINCode", array(
      "type"                => "text",
      "description"         => vt("ISIN-code"),
      "descriptionShort"    => vt("ISIN-code"),
      "visible"             => false,
      "sort"                => true,
      "widthClass"          => "w90",
    ));
    $this->addColomnDef("rating", array(
      "type"                => "text",
      "description"         => vt("Rating"),
      "descriptionShort"    => vt("Rating"),
      "sort"                => true,
      "visible"             => false,
      "widthClass"          => "w40",
    ));
  
  
    $this->addColomnDef("consolidatie", array(
      "type"                => "text",
      "description"         => vt("Consolidatie"),
      "descriptionShort"    => vt("Consolidatie"),
      "sort"                => true,
      "visible"             => false,
      "widthClass"          => "w40",
    ));
  
  
  }
}

?>