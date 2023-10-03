<?php
/*
    AE-ICT CODEX source module versie 1.6, 26 februari 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/05/14 15:26:21 $
    File Versie         : $Revision: 1.6 $

    $Log: emittentPerFonds.php,v $
    Revision 1.6  2014/05/14 15:26:21  rvv
    *** empty log message ***

    Revision 1.5  2014/05/10 13:51:28  rvv
    *** empty log message ***

    Revision 1.4  2011/08/31 15:18:50  rvv
    *** empty log message ***

    Revision 1.3  2011/06/02 14:58:31  rvv
    *** empty log message ***

    Revision 1.2  2011/04/17 08:50:34  rvv
    *** empty log message ***

    Revision 1.1  2011/02/26 15:49:19  rvv
    *** empty log message ***



*/

class EmittentPerFonds extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function EmittentPerFonds()
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
		($this->get("vermogensbeheerder")=="")?$this->setError("vermogensbeheerder", vt("Mag niet leeg zijn!")):true;
		($this->get("fonds")=="")?$this->setError("fonds", vt("Mag niet leeg zijn!")):true;
		//($this->get("depotbank")=="")?$this->setError("depotbank","Mag niet leeg zijn!"):true;
		//($this->get("emittent")=="")?$this->setError("emittent","Mag niet leeg zijn!"):true;
		//($this->get("rekenmethode")=="")?$this->setError("rekenmethode","Mag niet leeg zijn!"):true;

  	$query  = "SELECT id FROM emittentPerFonds WHERE ".
							" vermogensbeheerder = '".$this->get("vermogensbeheerder")."' AND ".
							" fonds = '".$this->get("fonds")."'";// AND ".
		//					" depotbank = '".$this->get("depotbank")."' AND ".
		//					" emittent = '".$this->get("emittent")."' ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("vermogensbeheerder", vt("deze combinatie bestaat al"));
			$this->setError("fonds", vt("deze combinatie bestaat al"));
		//	$this->setError("emittent","deze combinatie bestaat al");
		//	$this->setError("depotbank","deze combinatie bestaat al");
		//	$this->setError("Vanaf","deze combinatie bestaat al");
		}
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
    $this->data['table']  = "emittentPerFonds";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

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

		$this->addField('emittent',
													array("description"=>"emittent",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT emittent, emittent FROM emittenten order by emittent",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"emittenten"));

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Omschrijving",
													'select_query_ajax' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE Fonds='{value}'",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('vermogensbeheerder',
													array("description"=>"vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Vermogensbeheerder, Vermogensbeheerder FROM Vermogensbeheerders order by Vermogensbeheerder",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));
/*
		$this->addField('depotbank',
													array("description"=>"depotbank",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Depotbank, Depotbank FROM Depotbanken order by Depotbank",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Depotbanken"));

		$this->addField('rekenmethode',
													array("description"=>"rekenmethode",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
													"form_options"=>array(1=>'Beginvermogen',2=>'Eindvermogen',3=>'Gemiddeld vermogen',4=>'3 maands ultimo',5=>'Dagelijks gemiddelde'),
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('percentage',
													array("description"=>"percentage",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.4f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
*/
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