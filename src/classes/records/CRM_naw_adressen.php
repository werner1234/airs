<?php
/*
    AE-ICT CODEX source module versie 1.6, 3 februari 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/10/31 17:17:18 $
    File Versie         : $Revision: 1.10 $

    $Log: CRM_naw_adressen.php,v $
    Revision 1.10  2018/10/31 17:17:18  rvv
    *** empty log message ***

    Revision 1.9  2017/12/14 09:37:23  rvv
    *** empty log message ***

    Revision 1.8  2017/12/03 10:32:36  rvv
    *** empty log message ***

    Revision 1.7  2016/05/04 16:21:46  rvv
    *** empty log message ***

    Revision 1.6  2015/11/08 16:39:40  rvv
    *** empty log message ***

    Revision 1.5  2014/05/08 04:08:56  rvv
    *** empty log message ***

    Revision 1.4  2011/10/23 13:19:21  rvv
    *** empty log message ***

    Revision 1.3  2011/09/28 18:40:59  rvv
    *** empty log message ***

    Revision 1.2  2011/05/25 17:25:21  rvv
    *** empty log message ***

    Revision 1.1  2010/02/03 17:13:34  rvv
    *** empty log message ***



*/

class CRM_naw_adressen extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_naw_adressen()
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
		($this->get("naam")=="")?$this->setError("naam",vt("Mag niet leeg zijn!")):true;
    $fields = array_keys($this->data['fields']);
    foreach($fields as $field)
    {
      if($this->data['fields'][$field]['validate_notEmpty'])
      {
        if($this->get($field)=='')
           $this->setError($field,vt("Mag niet leeg zijn."));
      }
    }
		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	 $level = GetCRMAccess();
	  switch ($type)
	  {
	  	case "edit":
	  		return ($level >=3 )?true:false;
	  		break;
	  	case "delete":
	  		return ($level >=7 )?true:false;
	  		break;
	  	default:
	  	  return false;
	  		break;
	  }
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "CRM_naw_adressen";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rel_id',
													array("description"=>"rel_id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('voornamen',
                    array("description"=>"Voornamen",
                          "default_value"=>"",
                          "db_size"=>"255",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"60",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
		$this->addField('naam',
													array("description"=>"Naam",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('naam1',
													array("description"=>"Naam1",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('verzendAanhef',
													array("description"=>"VerzendAanhef",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('adres',
													array("description"=>"Adres",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('pc',
													array("description"=>"Postcode",
													"default_value"=>"",
													"db_size"=>"17",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"17",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('plaats',
													array("description"=>"Plaats",
													"default_value"=>"",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"30",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('land',
													array("description"=>"Land",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('evenement',
													array("description"=>"Evenement",
													"default_value"=>"",
													"db_size"=>"30",
													"db_type"=>"varchar",
													 "select_query"=>"SELECT omschrijving,omschrijving  FROM CRM_selectievelden WHERE  module = 'evenementen' UNION SELECT 'rapportage','rapportage'",
													"form_type"=>"selectKeyed",
													"form_size"=>"30",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memo',
													array("description"=>"Memo",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('email',
													array("description"=>"Email",
													"default_value"=>"",
													"db_size"=>"64",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"32",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('wachtwoord',
													array("description"=>"Wachtwoord",
													"default_value"=>"",
													"db_size"=>"32",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"32",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('rapportage',
										array("description"=>"Rapportage",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"selectKeyed",
													"form_options"=>array(1=>'rapportage'),
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
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

		$this->addField('add_date',
													array("description"=>"add_date",
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

		$this->addField('geboortedatum',
													array("description"=>"geboortedatum",
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
													"list_order"=>"true",
													"categorie"=>"hidden"));

		$this->addField('verjaardagLijst',
													array("description"=>"In verjaardagslijst",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"hidden"));


    $db=new DB();
    $db->SQL("DESC ".$this->data['table']);
    $db->Query();
    $addedFields=array_keys($this->data['fields']);
    while($data=$db->nextRecord())
    {
      if(!in_array($data['Field'],$addedFields))
      		$this->addField($data['Field'],
													array("description"=>$data['Field'],
													"default_value"=>"",
													"db_size"=>$data['db_size'],
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>$data['db_size'],
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
      
    }
  }
}
?>