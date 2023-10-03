<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 december 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/07/08 08:19:33 $
    File Versie         : $Revision: 1.5 $
 		
    $Log: FondsExtraVelden.php,v $
    Revision 1.5  2018/07/08 08:19:33  rvv
    *** empty log message ***

    Revision 1.4  2018/01/04 07:38:50  rvv
    *** empty log message ***

    Revision 1.3  2018/01/04 05:55:24  rvv
    *** empty log message ***

    Revision 1.2  2018/01/03 14:17:55  rvv
    *** empty log message ***

    Revision 1.1  2017/12/20 16:57:25  rvv
    *** empty log message ***

 		
 	
*/

class FondsExtraVelden extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function FondsExtraVelden()
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

		$db=new DB();

		if(ereg("[^A-Za-z0-9_]",$this->get('veldnaam')))
			$this->setError('veldnaam',vt("Het veld bevatte ongeldige tekens, deze zijn verwijderd."));

		$this->set('veldnaam',ereg_replace("[^A-Za-z0-9_]", "", $this->get('veldnaam')));

		$query="SHOW COLUMNS FROM FondsExtraInformatie LIKE '".$this->get('veldnaam')."'";
		if($db->QRecords($query) && $this->get('id') < 1)
			$this->setError('veldnaam', vt("Het opgegeven veld zit al in de database. (FondsExtraInformatie tabel)"));


		if($this->get('omschrijving')=="")
			$this->setError('omschrijving',"Mag niet leeg zijn.");

		($this->get("veldnaam")=="")?$this->setError("veldnaam",vt("Mag niet leeg zijn!")):true;
		($this->get("veldtype")=="")?$this->setError("veldtype",vt("Mag niet leeg zijn!")):true;

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
		if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren']==1)
			return true;
	  return checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "FondsExtraVelden";
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

		$this->addField('veldnaam',
													array("description"=>"Veldnaam",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"30",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('omschrijving',
										array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('veldtype',
													array("description"=>"Veldtype",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_options"=>array('Tekst','Memo','Getal','Datum','Trekveld','Checkbox','Document'),
													"form_type"=>"select",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('volgorde',
													array("description"=>"Volgorde",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('inActief',
										array("description"=>"Inactief",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"int",
													"form_type"=>"checkbox",
													"form_size"=>"3",
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