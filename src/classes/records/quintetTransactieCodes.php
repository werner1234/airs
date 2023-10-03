<?php
/*
    AE-ICT sourcemodule created 15 nov 2021
    Author              : Lennart Poot

    call 9813

*/

class quintetTransactieCodes extends Table
{

  var $data = array();
  var $tableName = "quintetTransactieCodes";

  function quintetTransactieCodes()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
    $this->initModule();
  }

  function addField($name, $properties)
  {
    $this->data['fields'][$name] = $properties;
  }

  /*
   * Veldvalidatie
   */
  function validate()
  {
    ($this->get("bankCode")=="")?$this->setError("bankCode",vt("Mag niet leeg zijn!")):true;
    ($this->get("omschrijving")=="")?$this->setError("omschrijving",vt("Mag niet leeg zijn!")):true;

    $valid = ($this->error==false)?true:false;
    return $valid;
  }

  /*
   * Toegangscontrole
   */
  function checkAccess($type)
  {
    return true;
  }

  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);
    $tst->changeField($this->tableName,"omschrijving",array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,"bankCode",array("Type"=>"varchar(26)","Null"=>false));
    $tst->changeField($this->tableName,"doActie",array("Type"=>"varchar(10)","Null"=>false));

  }


  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']     = "quintetTransactieCodes";
    $this->data['table']    = $this->tableName;
    $this->data['identity'] = "id";

    $this->addField('id',
                    array("description"=>"id",
                          "default_value"=>"",
                          "db_size"=>"11",
                          "db_type"=>"int",
                          "form_type"=>"text",
                          "form_size"=>"11",
                          "form_visible"=>false,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('change_user',
                    array("description"=>"change_user",
                          "default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"10",
                          "form_visible"=>false,
                          "list_visible"=>false,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('change_date',
                    array("description"=>"change_date",
                          "default_value"=>"",
                          "db_size"=>"0",
                          "db_type"=>"datetime",
                          "form_type"=>"calendar",
                          "form_size"=>"0",
                          "form_visible"=>false,
                          "list_visible"=>false,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('add_user',
                    array("description"=>"add_user",
                          "default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"10",
                          "form_visible"=>false,
                          "list_visible"=>false,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('add_date',
                    array("description"=>"add_date",
                          "default_value"=>"",
                          "db_size"=>"0",
                          "db_type"=>"datetime",
                          "form_type"=>"calendar",
                          "form_size"=>"0",
                          "form_visible"=>false,
                          "list_visible"=>false,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('bankCode',
                    array("description"=>"bankCode",
                          "default_value"=>"",
                          "db_size"=>"26",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('omschrijving',
                    array("description"=>"omschrijving",
                          "default_value"=>"",
                          "db_size"=>"50",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"50",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('doActie',
                    array("description"=>"doActie",
                          "default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "form_options" => array(
                            "A"        => "A &nbsp;- Aankoop van stukken",
                            "BEH"      => "BEH &nbsp;- beheerfee",
                            "DIV"      => "DIV - Contant dividend",
                            "FX"       => "FX",
                            "GELDMUT"  => "GELDMUT - Geldmutaties",
                            "KNBA"     => "KNBA &nbsp;- bankkosten",
                            "R"        => "R &nbsp;- Rente op geldrekeningen",
                            "RENOB"    => "RENOB&nbsp;- Coupons",
                            "V"        => "V &nbsp;- Verkoop van stukken",
                            ""         => "------------------------------",
                            "NVT"      => "N.v.t."),

                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>false,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>true,
                          "list_order"=>"true"));



  }
}
