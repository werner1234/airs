<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2017/01/18 08:19:15 $
    File Versie         : $Revision: 1.8 $

    $Log: htmlFondsOverzicht.php,v $
    Revision 1.8  2017/01/18 08:19:15  rm
    Html rapportage

    Revision 1.7  2017/01/16 15:48:03  cvs
    call 5583

    Revision 1.6  2017/01/13 14:06:19  rm
    Html rapportage

    Revision 1.5  2017/01/12 14:25:52  rm
    Html rapportage

    Revision 1.4  2017/01/11 14:23:24  rm
    Html rapportage

    Revision 1.3  2016/12/22 09:39:52  rm
    Html rapportage

    Revision 1.2  2016/12/21 16:34:23  rm
    Html rapport

    Revision 1.1  2016/12/07 08:46:51  cvs
    call 5469




*/

class htmlFondsOverzicht extends AE_cls_htmlColomns
{
  var $user;
  var $tableName = "_htmlRapport_FondsOverzicht";
  var $vermogenbeheerder;
  var $properties = array(
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
  var $statics;

  function htmlFondsOverzicht()
  {
    global $USR;
    $this->initModule();
    $this->user = $USR;
    $this->defineData();
    $this->defineSorting();
    $this->defineEndTotal();
    $this->db = new DB($this->dbId);
  }


  function clearTable()
  {
    // verwijder historie ouder dan vandaag
    $query = "DELETE FROM `".$this->tableName."` WHERE `add_date` <= '".date("Y-m-d", mktime()-86400)." 23:59:59' ";

    $this->db->executeQuery($query);
    // verwijder historie van portefeuille
    $query = "DELETE FROM `".$this->tableName."` WHERE `add_user` = '".$this->user."' ";//AND `vermogensBeheerder` = '".$this->vermogenbeheerder."'
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
    $tst->tableExist("$this->tableName",true);
    $tst->changeField("$this->tableName","vermogensBeheerder",array("Type"=>"varchar(10)","Null"=>false));
    $tst->changeField("$this->tableName","Portefeuille",array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField("$this->tableName","Rapport",array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField("$this->tableName","Client",array("Type"=>"varchar(20)","Null"=>false));
    $tst->changeField("$this->tableName","Naam",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("$this->tableName","Naam1",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("$this->tableName","Fonds",array("Type"=>"varchar(25)","Null"=>false));
    $tst->changeField("$this->tableName","fondsISIN",array("Type"=>"varchar(25)","Null"=>false));
    $tst->changeField("$this->tableName","accountmanager",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("$this->tableName","depotbank",array("Type"=>"varchar(12)","Null"=>false));
    $tst->changeField("$this->tableName","FondsOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("$this->tableName","Kostprijs",array("Type"=>"double","Null"=>false));
    $tst->changeField("$this->tableName","HistorischeWaarde",array("Type"=>"double","Null"=>false));
    $tst->changeField("$this->tableName","AandeelBeleggingscategorie",array("Type"=>"double","Null"=>false));
    $tst->changeField("$this->tableName","AandeelTotaalvermogen",array("Type"=>"double","Null"=>false));
    $tst->changeField("$this->tableName","AandeelTotaalBelegdvermogen",array("Type"=>"double","Null"=>false));
    $tst->changeField("$this->tableName","AantalInPortefeuille",array("Type"=>"double","Null"=>false));
    $tst->changeField("$this->tableName","memo",array("Type"=>"text","Null"=>false));
    $tst->changeField("$this->tableName","risicoklasse",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField("$this->tableName","soortOvereenkomst",array("Type"=>"varchar(30)","Null"=>false));
    $tst->changeField("$this->tableName","actueleWaarde",array("Type"=>"double","Null"=>false));

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
    // depotbank / accountmanager / overieenkomst / profiel
    $this->addSortDef("depotbank",array(
      "orderField"       => "depotbank",
      "descriptionField" => "depotbank",
      "orderBreakField"  => "depotbank",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));

    $this->addSortDef("accountmanager",array(
      "orderField"       => "accountmanager",
      "descriptionField" => "accountmanager",
      "orderBreakField"  => "accountmanager",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));

      $this->addSortDef("soortOvereenkomst",array(
      "orderField"       => "soortOvereenkomst",
      "descriptionField" => "soortOvereenkomst",
      "orderBreakField"  => "soortOvereenkomst",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));



    $this->addSortDef("risicoklasse",array(
      "orderField"       => "risicoklasse",
      "descriptionField" => "risicoklasse",
      "orderBreakField"  => "risicoklasse",
      "orderIncremental" => true,
      "disable"          => false,
      "headerClass"      => "headerTree",
      "prio"             => 1,
    ));

  }
  
  function defineData()
  {
    global $__appvar;
		$this->addColomnDef("vermogensBeheerder", array(
      "description"      => vt("vermogensBeheerder"),
      "descriptionShort" => vt("vermogensBeheerder"),
    ));

    $this->addColomnDef("Portefeuille", array(
      "description"       => vt("Portefeuille"),
      "descriptionShort"  => vt("Portefeuille"),
      "sort"              => true,
      'visible'           => true,
//      'clickable' => $__appvar['baseurl'] . '/rapportFrontofficeHtml_fondsoverzicht.php?vb={Vermogensbeheerder}&fonds={fonds}'
    ));


    $this->addColomnDef("Rapport", array(
      "description"      => vt("Rapport"),
      "descriptionShort" => vt("Rapport"),
      "sort"             => true,
    ));


    $this->addColomnDef("Client", array(
      "description"      => vt("Client"),
      "descriptionShort" => vt("Client"),
      "sort"             => true,
    ));


    $this->addColomnDef("Naam", array(
      "description"       => vt("Naam"),
      "descriptionShort"  => vt("Naam"),
      "sort"              => true,
      "visible"           => true,
    ));


    $this->addColomnDef("Naam1", array(
      "description"      => vt("Naam1"),
      "descriptionShort" => vt("Naam1"),
      "sort"             => true,
    ));


    $this->addColomnDef("Fonds", array(
      "description"      => vt("Fonds"),
      "descriptionShort" => vt("Fonds"),
      "sort"             => true,
    ));

    $this->addColomnDef("FondsOmschrijving", array(
      "description"      => vt("FondsOmschrijving"),
      "descriptionShort" => vt("FondsOmschrijving"),
      "sort"             => true,
    ));

    $this->addColomnDef("fondsISIN", array(
      "description"      => vt("fondsISIN"),
      "descriptionShort" => vt("fondsISIN"),
      "sort"             => true,
    ));

    $this->addColomnDef("accountmanager", array(
      "description"      => vt("Accountmanager"),
      "descriptionShort" => vt("Accountmanager"),
    ));

    $this->addColomnDef("memo", array(
      "description"      => vt("memo"),
      "descriptionShort" => vt("memo"),
      "sort"             => true,
    ));
    $this->addColomnDef("risicoklasse", array(
      "description"      => vt("risicoklasse"),
      "descriptionShort" => vt("risicoklasse"),
      "sort"             => true,
    ));

    $this->addColomnDef("soortOvereenkomst", array(
      "description"      => vt("Overeenkomst"),
      "descriptionShort" => vt("Overeenkomst"),
      "visible"          => true,
    ));

    $this->addColomnDef("Kostprijs", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("Kostprijs"),
      "descriptionShort" => vt("Kostprijs"),
      "widthClass"       => "w75", 
      "displayFormat"    => "@N{.2}",
      "sumFormat"        => "@N{.2}",
      "sumTotalFormat"   => "@N{.2}",
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",

    ));

    $this->addColomnDef("HistorischeWaarde", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("HistorischeWaarde"),
      "descriptionShort" => vt("HistorischeWaarde"),
      "widthClass"       => "w75",
      "displayFormat"    => "@N{.2}",
      "sumFormat"        => "@N{.2}",
      "sumTotalFormat"   => "@N{.2}",
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",

    ));

    $this->addColomnDef("AandeelBeleggingscategorie", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("AandeelBeleggingscategorie"),
      "descriptionShort" => vt("%Beleggings- %s categorie", array('<br />')),
      "widthClass"       => "w75",
      "displayFormat"    => "@N{.2}%",
      "sumFormat"        => "@N{.2}%",
      "sumTotalFormat"   => "@N{.2}%",
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",
      "headerClass"      => "headerRight",

    ));

    $this->addColomnDef("AandeelTotaalvermogen", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("%Totaal Vermogen"),
      "descriptionShort" => vt("%Totaal Vermogen"),
      "widthClass"       => "w75",
      "displayFormat"    => "@N{.2}%",
      "sumFormat"        => "@N{.2}%",
      "sumTotalFormat"   => "@N{.2}%",
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",

    ));

    $this->addColomnDef("AandeelTotaalBelegdvermogen", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("AandeelTotaalBelegdvermogen"),
      "descriptionShort" => vt("AandeelTotaalBelegdvermogen"),
      "widthClass"       => "w75",
      "displayFormat"    => "@N{.2}%",
      "sumFormat"        => "@N{.2}%",
      "sumTotalFormat"   => "@N{.2}%",
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",
      'visible'         => false,
      'hideColumn'      => true

    ));


    $this->addColomnDef("AantalInPortefeuille", array(
      "type"             => "number",
      "formatClass"      => "attNumber",
      "description"      => vt("Aantal"),
      "descriptionShort" => vt("Aantal"),
      "widthClass"       => "w75",
      "displayFormat"    => "@N{.2}",
      "sumFormat"        => "@N{.2}",
      "sumTotalFormat"   => "@N{.2}",
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",
      "sumTotal"         => true,
      'visible'           => true,
    ));


  $this->addColomnDef("risicoklasse", array(
      "type"             => "text",
      "description"      => vt("Risicoprofiel"),
      "widthClass"       => "w100",
    ));
  $this->addColomnDef("depotbank", array(
      "type"             => "text",
      "description"      => vt("Depotbank"),
      "widthClass"       => "w100",
    ));

 $this->addColomnDef("actueleWaarde", array(
      "type"             => "text",
      "formatClass"      => "attNumber",
      "description"      => vt("Actuelewaarde"),
      "widthClass"       => "w100",
      "widthClass"       => "w75",
      "displayFormat"    => "@N{.2}",
      "sumFormat"        => "@N{.2}",
      "sumTotalFormat"   => "@N{.2}",
      "headerClass"      => "headerRight",
      "widthClass"       => "w100",

      "sumTotal"         => true,
      'visible'          => true,
    ));






  }
}
?>