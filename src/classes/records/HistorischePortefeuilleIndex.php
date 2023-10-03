<?php
/*
    AE-ICT CODEX source module versie 1.6, 21 november 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/10/13 17:15:10 $
    File Versie         : $Revision: 1.7 $

    $Log: HistorischePortefeuilleIndex.php,v $
    Revision 1.7  2018/10/13 17:15:10  rvv
    *** empty log message ***

    Revision 1.6  2017/06/14 16:04:42  rvv
    *** empty log message ***

    Revision 1.5  2016/03/28 14:24:06  rvv
    *** empty log message ***

    Revision 1.4  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.3  2013/07/17 15:49:49  rvv
    *** empty log message ***

    Revision 1.2  2011/01/26 17:18:58  rvv
    *** empty log message ***

    Revision 1.1  2010/11/21 13:05:20  rvv
    *** empty log message ***



*/

class HistorischePortefeuilleIndex extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function HistorischePortefeuilleIndex()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
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

    $valid = ($this->error==false)?true:false;
    return $valid;
  }

  /*
   * Toegangscontrole
   */
  function checkAccess($type)
  {
    return checkAccess($type);
  }

  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "HistorischePortefeuilleIndex";
    $this->data['identity'] = "id";

    $this->addField('id',
      array("description"=>"id",
        "default_value"=>"",
        "db_size"=>"11",
        "db_type"=>"int",
        "form_type"=>"text",
        "form_size"=>"11",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('Portefeuille',
      array("description"=>"Portefeuille",
        "default_value"=>"",
        "db_size"=>"24",
        "db_type"=>"varchar",
        "form_type"=>"text",
        "form_size"=>"24",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true",
        "keyIn"=>"Portefeuilles,GeconsolideerdePortefeuilles"));

    $this->addField('Datum',
      array("description"=>"Datum",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"date",
        "form_type"=>"calendar",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));


    $this->addField('periode',
      array("description"=>"Periode",
        "default_value"=>"",
        "db_size"=>"2",
        "db_type"=>"varchar",
        "form_type"=>"selectKeyed",
        "form_options"=>array('m'=>'Maand','dk'=>'Dagen Kwartaal','k'=>'Kwartaal','t'=>'TWR dag','dy'=>'Dag YTD','2w'=>'Halve maand','j'=>'Jaar'),
        "form_size"=>"2",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('IndexWaarde',
      array("description"=>"IndexWaarde",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));


    $this->addField('PortefeuilleWaarde',
      array("description"=>"PortefeuilleWaarde",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('Stortingen',
      array("description"=>"Stortingen",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('Onttrekkingen',
      array("description"=>"Onttrekkingen",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('Opbrengsten',
      array("description"=>"Opbrengsten",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('Kosten',
      array("description"=>"Kosten",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('Categorie',
      array("description"=>"Categorie",
        "default_value"=>"",
        "db_size"=>"15",
        "db_type"=>"varchar",
        "form_type"=>"text",
        "form_size"=>"15",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('PortefeuilleBeginWaarde',
      array("description"=>"PortefeuilleBeginWaarde",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('gemiddelde',
      array("description"=>"gemiddelde",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('gerealiseerd',
      array("description"=>"Gerealiseerd",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('ongerealiseerd',
      array("description"=>"Ongerealiseerd",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('rente',
      array("description"=>"Rente",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));


    $this->addField('extra',
      array("description"=>"Extra",
        "default_value"=>"",
        "db_type"=>"text",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>false,
        "list_visible"=>false,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('scenarioKansOpDoel',
      array("description"=>"Scenario kans op doel",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('scenarioVerwachtVermogen',
      array("description"=>"Scenario verwacht vermogen",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"double",
        "form_type"=>"text",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_format"=>"%01.2f",
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('scenarioProfiel',
      array("description"=>"Scenario profiel",
        "default_value"=>"",
        "db_size"=>"50",
        "db_type"=>"varchar",
        "form_type"=>"text",
        "form_size"=>"15",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>true,
        "list_order"=>"true"));

    $this->addField('add_date',
      array("description"=>"add_date",
        "default_value"=>"",
        "db_size"=>"0",
        "db_type"=>"datetime",
        "form_type"=>"calendar",
        "form_size"=>"0",
        "form_visible"=>true,
        "list_visible"=>true,
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
        "form_visible"=>true,
        "list_visible"=>true,
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
        "form_visible"=>true,
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
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

  }
}
?>