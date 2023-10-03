<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/02/11 07:09:11 $
    File Versie         : $Revision: 1.5 $
 		
    $Log: PortefeuilleHistorischeParameters.php,v $
    Revision 1.5  2019/02/11 07:09:11  rvv
    *** empty log message ***

    Revision 1.4  2019/02/10 14:27:56  rvv
    *** empty log message ***

    Revision 1.3  2019/02/09 18:43:28  rvv
    *** empty log message ***

    Revision 1.2  2019/02/06 15:53:42  rvv
    *** empty log message ***

    Revision 1.1  2017/09/02 17:17:46  rvv
    *** empty log message ***

 		
 	
*/

class PortefeuilleHistorischeParameters extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function PortefeuilleHistorischeParameters()
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
    return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "PortefeuilleHistorischeParameters";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portefeuille',
										array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Portefeuille, concat(Portefeuille,' - ',Client) FROM Portefeuilles WHERE  Einddatum > NOW() ORDER BY Portefeuille",
													"select_query_ajax"=>"SELECT Portefeuille, concat(Portefeuille,' - ',Client) FROM Portefeuilles WHERE Portefeuille='{value}' AND Einddatum > NOW()",
													"form_type"=>"selectKeyed",
													"form_size"=>"24",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));

		$this->addField('tot',
													array("description"=>"Gebruik tot",
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

		$this->addField('veld',
													array("description"=>"Veld",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options"=>array('SpecifiekeIndex'=>'SpecifiekeIndex','Risicoklasse'=>'Risicoprofiel','SoortOvereenkomst'=>'Soort overeenkomst'),
													"form_size"=>"50",
                          "form_extra"=>"onchange='javascript:waardenLaden(this.value);'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('waarde',
													array("description"=>"Waarde",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
							//						"form_type"=>"selectKeyed",
							//						'select_query' => "SELECT Fonds,Fonds FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
							//						'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          'keyCondition'=>'veld',
                          'keyConditionTranslation'=>array('Fondsen'=>'SpecifiekeIndex','Risicoklassen'=>'Risicoklasse','SoortOvereenkomsten'=>'SoortOvereenkomst'),
                          'extraKeyLookup'=>array('Risicoklassen'=>array('Vermogensbeheerder'=>'SELECT Portefeuille, \'Portefeuille\'  FROM Portefeuilles WHERE Vermogensbeheerder=\'{keyvalue}\'')),
                          'keyIn'=>'Fondsen,Risicoklassen,SoortOvereenkomsten'));

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