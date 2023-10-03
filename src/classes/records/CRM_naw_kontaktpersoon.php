<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/01/25 16:32:53 $
    File Versie         : $Revision: 1.14 $

    $Log: CRM_naw_kontaktpersoon.php,v $
    Revision 1.14  2020/01/25 16:32:53  rvv
    *** empty log message ***

    Revision 1.13  2017/12/16 18:34:42  rvv
    *** empty log message ***

    Revision 1.12  2017/12/13 17:00:57  rvv
    *** empty log message ***

    Revision 1.11  2016/07/13 08:14:08  rvv
    *** empty log message ***

    Revision 1.10  2016/05/04 16:21:46  rvv
    *** empty log message ***

    Revision 1.9  2015/11/08 16:39:40  rvv
    *** empty log message ***

    Revision 1.8  2011/10/23 13:19:21  rvv
    *** empty log message ***

    Revision 1.7  2011/03/13 18:33:46  rvv
    *** empty log message ***

    Revision 1.6  2010/10/21 16:16:18  rvv
    *** empty log message ***

    Revision 1.5  2010/03/17 15:01:27  rvv
    *** empty log message ***

    Revision 1.4  2009/10/21 16:10:45  rvv
    *** empty log message ***

    Revision 1.3  2007/10/09 06:17:58  cvs
    CRM rechten

    Revision 1.2  2007/08/02 14:14:04  rvv
    *** empty log message ***

    Revision 1.1  2006/01/05 16:00:34  cvs
    *** empty log message ***

    Revision 1.1.1.1  2005/12/06 18:20:55  cvs
    no message

    Revision 1.2  2005/11/21 10:08:25  cvs
    *** empty log message ***

    Revision 1.1  2005/11/17 08:10:04  cvs
    *** empty log message ***



*/

class CRM_naw_kontaktpersoon extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_naw_kontaktpersoon()
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
		if($_SESSION['usersession']['superuser'])
		  return true;
		else
		{
		  switch ($type)
		  {
		    case "edit":
          return GetCRMAccess(1);
          break;
        case "delete":
          return GetCRMAccess(2);		
          break;
        default:
          return false;
          break;
      }
		}

	}

	/*
  * Table definition
  */
  function defineData()
  {
		$this->omschrijving='Relaties';
    $this->data['name']  = "Kontaktpersonen";
    $this->data['table']  = "CRM_naw_kontaktpersoon";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>false,
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

		$this->addField('naam',
													array("description"=>"naam",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"tinytext",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('naam1',
													array("description"=>"naam1",
													"default_value"=>"",
													"db_size"=>"60",
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
													array("description"=>"adres",
													"default_value"=>"",
													"db_size"=>"60",
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
													array("description"=>"postcode / plaats",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	 $this->addField('plaats',
													array("description"=>"plaats",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
	 $this->addField('land',
													array("description"=>"land",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('sortering',
													array("description"=>"sortering",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('functie',
													array("description"=>"functie",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('tel1',
													array("description"=>"telefoon",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('tel1_oms',
													array("description"=>"tel1_oms",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",

													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('tel2',
													array("description"=>"telefoon 2",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('tel2_oms',
													array("description"=>"tel2_oms",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('fax_nr',
													array("description"=>"fax nr",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('email',
													array("description"=>"email",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"tinytext",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('crm_password',
													array("description"=>"crm_password",
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

		$this->addField('crm_login',
													array("description"=>"crm_login",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('crm_lastseen',
													array("description"=>"crm_lastseen",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
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
													"db_type"=>"date",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memo',
													array("description"=>"memo",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_rows"=>"10",
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
													"form_type"=>"datum",
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


		$this->addField('paspoortNummer',
										array("description"=>"paspoort nummer",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('geboortedatum',
										array("description"=>"geboortedatum",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('nationaliteit',
										array("description"=>"nationaliteit",
													"default_value"=>"Nederlandse",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('paspoortGeldigTot',
										array("description"=>"paspoort geldig tot",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

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
  
    $this->addField('contactpersoonUBO',
                    array("description"=>"contactpersoonUBO",
                          "default_value"=>"",
                          "db_size"=>"1",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_size"=>"1",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    
    $db=new DB();
    $db->SQL("DESC ".$this->data['table']);
    $db->Query();
    $addedFields=array_keys($this->data['fields']);
    while($data=$db->nextRecord())
    {

			if(!isset( $this->data['fields'][$data['Field']]))
			{
				preg_match("/^.*\\(([0-9]*)\\).*/", $data['Type'], $matches);
				if (intval($matches[1]) > 0)
				{
					$size = intval($matches[1]);
				}
				else
				{
					$size = 100;
				}

				if (!in_array($data['Field'], $addedFields))
				{
					$this->addField($data['Field'],
													array("description"   => $data['Field'],
																"default_value" => "",
																"db_size"       => $size,
																"db_type"       => "varchar",
																"form_type"     => "text",
																"form_size"     => $size,
																"form_visible"  => true,
																"list_visible"  => true,
																"list_width"    => "100",
																"list_align"    => "left",
																"list_search"   => false,
																"list_order"    => "true"));
				}
			}
      
    }
  
   
  }
}
?>