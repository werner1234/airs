<?php

/* 	
    AE-ICT CODEX source module versie 1.6, 1 juni 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/01/04 13:02:08 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: tgc_ipAccessList.php,v $
    Revision 1.1  2017/01/04 13:02:08  cvs
    call 5542, uitrol WWB en TGC

 		
 	
*/

class Tgc_ipAccessList extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Tgc_ipAccessList()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'], 0);
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
    $ip = $this->get("ip");
    if (trim($ip) == "" )
    {
      $this->setError("ip", vt("Het IP adres mag niet leeg zijn!"));
      return false;
    }

    $ipParts = explode(".",$ip);
    if (count($ipParts) != 4)
    {
      $this->setError("ip", vt("verkeerd formaat moet xxx.xxx.xxx.xxx zijn!"));
      return false;
    }

    if ($ipParts[0] < 1 OR $ipParts[0] > 255 OR !isNumeric($ipParts[0]) OR
        $ipParts[1] < 0 OR $ipParts[1] > 255 OR !isNumeric($ipParts[1]) OR
        $ipParts[2] < 0 OR $ipParts[2] > 255 OR !isNumeric($ipParts[2]) OR
        $ipParts[3] < 0 OR $ipParts[3] > 255 OR !isNumeric($ipParts[3])
    )
    {
      $this->setError("ip", vt("Geen geldig IP v4 adres!"));
      return false;
    }

    if (
      ($ipParts[0] == "10" )  OR
      ($ipParts[0] == "172" AND $ipParts[1] == "16")  OR
      ($ipParts[0] == "192" AND $ipParts[1] == "168")
    )
    {
      $this->setError("ip", vt("Lokale ip adressen niet toegestaan!"));
      return false;
    }






    //($this->get("ip") == "")?$this->setError("ip", vt("Mag niet leeg zijn!")):true;
    ($this->get("locatie") == "")?$this->setError("locatie", vt("Mag niet leeg zijn!")):true;

    $valid = ($this->error == false)?true:false;
    
    return $valid;
  }
  
  /*
   * Toegangscontrole
   */
  function checkAccess($type)
  {
    return true;
  }
  
  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name'] = "";
    $this->data['table'] = "tgc_ipAccessList";
    $this->data['identity'] = "id";
    
    $this->addField('id',
                    array("description"   => "id",
                          "default_value" => "",
                          "db_size"       => "11",
                          "db_type"       => "int",
                          "form_type"     => "text",
                          "form_size"     => "11",
                          "form_visible"  => false,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    $this->addField('change_user',
                    array("description"   => "change_user",
                          "default_value" => "",
                          "db_size"       => "10",
                          "db_type"       => "varchar",
                          "form_type"     => "text",
                          "form_size"     => "10",
                          "form_visible"  => false,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    $this->addField('change_date',
                    array("description"   => "change_date",
                          "default_value" => "",
                          "db_size"       => "0",
                          "db_type"       => "datetime",
                          "form_type"     => "calendar",
                          "form_size"     => "0",
                          "form_visible"  => false,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    $this->addField('add_user',
                    array("description"   => "add_user",
                          "default_value" => "",
                          "db_size"       => "10",
                          "db_type"       => "varchar",
                          "form_type"     => "text",
                          "form_size"     => "10",
                          "form_visible"  => false,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    $this->addField('add_date',
                    array("description"   => "add_date",
                          "default_value" => "",
                          "db_size"       => "0",
                          "db_type"       => "datetime",
                          "form_type"     => "calendar",
                          "form_size"     => "0",
                          "form_visible"  => false,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    $this->addField('ip',
                    array("description"   => "ip",
                          "default_value" => "",
                          "db_size"       => "20",
                          "db_type"       => "varchar",
                          "form_type"     => "text",
                          "form_size"     => "20",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => true,
                          "list_order"    => "true"));
    
    $this->addField('locatie',
                    array("description"   => "locatie",
                          "default_value" => "",
                          "db_size"       => "60",
                          "db_type"       => "varchar",
                          "form_type"     => "text",
                          "form_size"     => "60",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => true,
                          "list_order"    => "true"));
    
    $this->addField('bedrijf',
                    array("description"   => "bedrijf",
                          "default_value" => "",
                          "db_size"       => "15",
                          "db_type"       => "varchar",
                          "form_type"     => "text",
                          "form_size"     => "15",
                          "form_visible"  => false,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    $this->addField('onlineDatum',
                    array("description"   => "onlineDatum",
                          "default_value" => "",
                          "db_size"       => "0",
                          "db_type"       => "date",
                          "form_type"     => "calendar",
                          "form_size"     => "0",
                          "form_class"    => "AIRSdatepicker",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    $this->addField('offlineDatum',
                    array("description"   => "offlineDatum",
                          "default_value" => "",
                          "db_size"       => "0",
                          "db_type"       => "date",
                          "form_type"     => "calendar",
                          "form_size"     => "0",
                          "form_class"    => "AIRSdatepicker",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    $this->addField('loginVan',
                    array("description"   => "loginVan",
                          "default_value" => "",
                          "db_size"       => "0",
                          "db_type"       => "time",
                          "form_type"     => "text",
                          "form_size"     => "0",
                          "form_class"    => "",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    $this->addField('loginTot',
                    array("description"   => "loginTot",
                          "default_value" => "",
                          "db_size"       => "0",
                          "db_type"       => "time",
                          "form_type"     => "text",
                          "form_size"     => "0",
                          "form_class"    => "",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    $this->addField('whitelist',
                    array("description"   => "whitelist",
                          "default_value" => "",
                          "db_size"       => "0",
                          "db_type"       => "tinyint",
                          "form_type"     => "checkbox",
                          "form_size"     => "0",
                          "form_class"    => "",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));

    $this->addField('memo',
                    array("description"   => "memo",
                          "default_value" => "",
                          "db_size"       => "60",
                          "db_type"       => "text",
                          "form_type"     => "textarea",
                          "form_size"     => "80",
                          "form_rows"     => "8",
                          "form_visible"  => true,
                          "list_visible"  => true,
                          "list_width"    => "100",
                          "list_align"    => "left",
                          "list_search"   => false,
                          "list_order"    => "true"));
    
    
  }
}

?>