<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 11 maart 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/03/11 15:10:28 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: modelPortefeuillesPerModelPortefeuille.php,v $
    Revision 1.1  2020/03/11 15:10:28  rvv
    *** empty log message ***

 		
 	
*/

class ModelPortefeuillesPerModelPortefeuille extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ModelPortefeuillesPerModelPortefeuille()
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

    if ( isset ($_GET['modelPortefeuilleComponent']) && ! empty ($_GET['modelPortefeuilleComponent']) ) {
      $db = new DB();
      $query = "SELECT * FROM `ModelPortefeuilles` WHERE `Portefeuille` = '" . mysql_real_escape_string($_GET['modelPortefeuilleComponent']) . "';";
      if ( $db->QRecords($query) == 0 ) {
        $this->setError("modelPortefeuilleComponent",vt("modelPortefeuilleComponent is onbekend!"));
      }
    } else {
      $this->setError("modelPortefeuilleComponent",vt("modelPortefeuilleComponent  mag niet leeg zijn!"));
    }

    if ( isset ($_GET['modelPortefeuille']) && ! empty ($_GET['modelPortefeuille']) ) {
      $db = new DB();
      $query = "SELECT * FROM `ModelPortefeuilles` WHERE `Portefeuille` = '" . mysql_real_escape_string($_GET['modelPortefeuille']) . "';";
      if ( $db->QRecords($query) == 0 ) {
        $this->setError("modelPortefeuille",vt("modelPortefeuille is onbekend!"));
      }
    } else {
      $this->setError("modelPortefeuille",vt("modelPortefeuille mag niet leeg zijn!"));
    }


		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "modelPortefeuillesPerModelPortefeuille";
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
/*
		$this->addField('vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
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
													"list_order"=>"true",
                          "keyIn"=>"Vermogensbeheerders"));
*/
		$this->addField('modelPortefeuille',
													array("description"=>"ModelPortefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                                "select_query"=>"SELECT ModelPortefeuilles.Portefeuille ,ModelPortefeuilles.Portefeuille FROM ModelPortefeuilles
        JOIN Portefeuilles ON ModelPortefeuilles.Portefeuille=Portefeuilles.Portefeuille
        WHERE Portefeuilles.Einddatum>now() AND ModelPortefeuilles.Fixed=3 ORDER BY Portefeuille",
                                "select_query_ajax"=>"SELECT Portefeuille,Portefeuille FROM ModelPortefeuilles WHERE ModelPortefeuilles.Fixed=3 AND Portefeuille='{value}'",
                                "form_type"=>"selectKeyed",
													"form_size"=>"24",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));

		$this->addField('modelPortefeuilleComponent',
													array("description"=>"ModelPortefeuilleComponent",
                                "db_size"=>"24",
                                "db_type"=>"varchar",
                                "select_query"=>"SELECT ModelPortefeuilles.Portefeuille,if(ModelPortefeuilles.fixed=1,concat(ModelPortefeuilles.Portefeuille,' (FX)'),concat(ModelPortefeuilles.Portefeuille,' (Dyn)')) AS displayline FROM ModelPortefeuilles
        JOIN Portefeuilles ON ModelPortefeuilles.Portefeuille=Portefeuilles.Portefeuille
        WHERE Portefeuilles.Einddatum>now() AND ModelPortefeuilles.Fixed<2 ORDER BY Portefeuille",
                                "select_query_ajax"=>"SELECT Portefeuille, if(ModelPortefeuilles.fixed=1,concat(Portefeuille,' (FX)'),concat(Portefeuille,' (Dyn)')) FROM ModelPortefeuilles WHERE Portefeuille='{value}'",
                                "form_type"=>"selectKeyed",
                                "form_visible"=>true,"list_width"=>"150",
                                "list_visible"=>true,
                                "list_align"=>"left",
                                "list_search"=>false,
                                "list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));

		$this->addField('percentage',
													array("description"=>"Percentage",
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

		$this->addField('vanaf',
													array("description"=>"Vanaf",
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