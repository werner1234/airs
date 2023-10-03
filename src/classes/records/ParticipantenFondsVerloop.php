<?php
/* 	
 		Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2018/01/10 16:11:44 $
 		File Versie					: $Revision: 1.8 $
 				
*/

class ParticipantenFondsVerloop extends Table
{
  /*
  * Object vars
  */
  var $data = array();
  
  /*
  * Constructor
  */
  function ParticipantenFondsVerloop()
  {
    $this->defineData();
    $this->set($this->data['identity'],0);
  }

  function addClass ($field, $class)
  {//form_extra
    if ( ! isset ($this->data['fields'][$field]['form_class']) || empty ($this->data['fields'][$field]['form_class']) )
    {
      $this->data['fields'][$field]['form_class'] = $class;
    } else {
       $this->data['fields'][$field]['form_class'] .= ' '.$class;
    }
  }
  
	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}
  
	function checkAccess($type)
	{
		return true;
	}
	
	function validate()
	{

		if ( isset($_POST['check_aantal_tt']) ) {
			if ( in_array($this->get("transactietype"), array('B', 'A', 'D', 'BK', 'H'))) {
				if ( (float)$this->get("aantal") < 0 ) {
					$this->setError("aantal",vt("Aantal dient positief ingevoerd te worden!"));
				}
			} else {
				if ( (float)$this->get("aantal") > 0 ) {
					$this->setError("aantal",vt("Aantal dient negatief ingevoerd te worden!"));

				}
			}
		}

    ($this->get("datum") == '') ? $this->setError("datum",vt("Mag niet leeg zijn!")):true;
		((float)$this->get("aantal") == '') ? $this->setError("aantal",vt("Mag niet leeg zijn!")):true;
		($this->get("participanten_id") == '') ? $this->setError("participanten_id",vt("Mag niet leeg zijn!")):true;
    ($this->get("transactietype") == '') ? $this->setError("transactietype",vt("Mag niet leeg zijn!")):true;
    ($this->get("transactietype") == '0') ? $this->setError("transactietype",vt("Mag niet leeg zijn!")):true;
    ((float)$this->get("koers") == '') ? $this->setError("koers",vt("Mag niet leeg zijn!")):true;
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "participantenFondsVerloop";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('participanten_id',
													array("description"=>"Particpant",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
    
		$this->addField('aantal',
													array("description"=>"Aantal",
													"db_size"=>"45",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
                          "form_class" => 'maskNumeric6DigitsAllowNegative',
													"form_format"=>"%f",
													"list_format"=>"%f",
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Fonds verloop"));
    
		$this->addField('koers',
													array("description"=>"Koers",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
                          "form_class" => 'maskNumeric6Digits',
//													"form_format"=>"%01.6f",
//													"list_format"=>"%01.6f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Fonds verloop"));
    
    $this->addField('transactietype',
													array("description"=>"Transactietype",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
                            "form_options" => array(
                              'B' => 'Begin',
                              'A' => 'Aankoop',
                              'V' => 'Verkoop',
                              'D' => 'Deponering',
                              'L' => 'Lichting',

															'BK'	=> 'Bijkopen',
															'DV'	=> 'Deelverkoop',
															'H'	=> 'Herbelegging',
															'U'	=> 'Uitkering',
                            ),
													"list_order"=>"true",
																"categorie"=>"Fonds verloop"));
    
    $this->addField('datum',
													array("description"=>"Datum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
                          "form_class"=> "AIRSdatepicker",
                          "form_extra"=>" onchange=\"date_complete(this);\"",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
																"categorie"=>"Fonds verloop"));
		$this->addField('omschrijving',
													array("description"=>"Omschrijving",
													"db_size"=>"50",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"",
													"list_width"=>"150",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
																"categorie"=>"Fonds verloop"));
    
		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('print_date',
										array("description"=>"Verzenddatum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Fonds verloop"));

		$this->addField('waarde',
										array("description"=>"Waarde",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
//													"form_class" => 'maskNumeric6DigitsAllowNegative',
//													"form_format"=>"%01.6f",
//													"list_format"=>"%01.6f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Fonds verloop"));



	}
}
?>