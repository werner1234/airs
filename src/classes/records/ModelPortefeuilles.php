<?php
/*
    AE-ICT CODEX source module versie 1.3, 5 december 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/10/07 19:41:38 $
    File Versie         : $Revision: 1.13 $

    $Log: ModelPortefeuilles.php,v $
    Revision 1.13  2015/10/07 19:41:38  rvv
    *** empty log message ***

    Revision 1.12  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.11  2014/11/30 13:04:47  rvv
    *** empty log message ***

    Revision 1.10  2014/09/24 15:46:00  rvv
    *** empty log message ***

    Revision 1.9  2013/12/09 06:58:09  rvv
    *** empty log message ***

    Revision 1.8  2013/12/08 13:00:47  rvv
    *** empty log message ***

    Revision 1.7  2013/11/02 16:58:18  rvv
    *** empty log message ***

    Revision 1.6  2013/08/21 11:40:46  rvv
    *** empty log message ***

    Revision 1.5  2011/04/27 17:51:37  rvv
    *** empty log message ***

    Revision 1.4  2009/09/26 08:52:47  rvv
    *** empty log message ***

    Revision 1.3  2008/05/16 08:10:25  rvv
    *** empty log message ***

    Revision 1.2  2007/08/02 14:14:04  rvv
    *** empty log message ***

    Revision 1.1  2006/12/11 11:01:50  rvv
    *** empty log message ***



*/

class ModelPortefeuilles extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function ModelPortefeuilles()
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
		$query  = "SELECT id FROM ModelPortefeuilles WHERE Portefeuille = '".$this->get("Portefeuille")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Portefeuille",vtb("%s is al gekoppeld", array($this->get("Portefeuille"))));
		}

		$DB->lookupRecordByQuery("SELECT Portefeuille FROM Portefeuilles WHERE Portefeuille = '".mysql_real_escape_string($this->get("Portefeuille"))."' ");
		if($DB->records() <= 0) {
			$this->setError("Portefeuille", vtb("%s is een onbekende portefeuille", array($this->get("Portefeuille"))));
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
    $this->data['table']  = "ModelPortefeuilles";
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

		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													'autocomplete' => array(
														'table'        => 'Portefeuilles',
														'prefix'       => true,
														'returnType'   => 'expanded',
														'extra_fields' => array(
															'Portefeuille',
															'Client',
															'id',
														),
														'label'        => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
														'searchable'   => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
														'field_value'  => array('Portefeuilles.Portefeuille'),
														'value'        => 'Portefeuilles.Portefeuille',
													),
													"form_size"=>"24",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));



		$this->addField('Omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Fixed',
													array("description"=>"Type",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"radio",
													"form_visible"=>true,
													"form_extra"=>"onClick=\"javascript:showFixed();\"",
													"form_options"=>array(0=>'Normaal',1=>'Fixed',2=>'Referentie',3=>'Meervoudig'),
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfdrukNiveau',
													array("description"=>"Afdruk niveau",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"radio",
													"form_visible"=>true,
													"form_extra"=>"",
													"form_options"=>array('Fonds'=>'Fonds',
                                                'beleggingscategorie'=>'Categorie',
                                                'beleggingssector'=>'Sector',
                                                'Regio'=>'Regio'),
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('FixedDatum',
													array("description"=>"Datum",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_select_option_notempty"=>true,
													"form_extra"=>"onChange=\"javascript:reloadFixed();\"",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));


			$this->addField('Beleggingscategorie',
													array("description"=>"Beleggingscategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Beleggingscategorie,Beleggingscategorie  FROM Beleggingscategorien ",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Beleggingscategorien"));

		$this->addField('VerwerkingsmethodeDiv',
													array("description"=>"DIV verwerking",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"radio",
													"form_visible"=>true,
													"form_extra"=>"",
													"form_options"=>array('0'=>'Reguliere verwerking',
                                                '1'=>'Verwerking excl DIVBE',
                                                '2'=>'Geen verwerking AIRS'),
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
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



  }
}
?>