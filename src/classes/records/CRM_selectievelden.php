<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/04/10 15:43:17 $
 		File Versie					: $Revision: 1.8 $

*/

class CRM_selectievelden extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_selectievelden()
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
    global $__CRMvars;
	  ($this->get("omschrijving")=="")?$this->setError("omschrijving",vt("Mag niet leeg zijn!")):true;
  	  $valid = ($this->error==false)?true:false;

  	  if($valid == true && ($_GET['key_waarde'] || $_GET['key_omschrijving']) && $this->get('id') > 0)
  	  {
  	    //listarray($__CRMvars['koppelingen'][$this->get('module')]);
  	    $db=new DB();
  	    foreach ($__CRMvars['koppelingen'][$this->get('module')] as $table=>$velden)
  	    {


  	      $query="SELECT waarde,omschrijving FROM CRM_selectievelden WHERE id='". $this->get('id')."'";
  	      $db->SQL($query);
  	      $db->Query();
  	      $oldData=$db->nextRecord();
  	      if($_GET['key_waarde'])
  	      {
  	        $nieuweWaarde=$this->get('waarde');
  	        $oudeWaarde=$oldData['waarde'] ;
  	      }
  	      else
  	      {
  	        $nieuweWaarde=$this->get('omschrijving');
  	        $oudeWaarde=$oldData['omschrijving'] ;
  	      }
  	      $veldenQuery='';

  	      foreach ($velden as $veld)
  	      {
  	        $query="UPDATE $table SET $veld='$nieuweWaarde' WHERE $veld='$oudeWaarde'";
  	        $db->SQL($query);
  	        $db->Query();
            //echo $query."<br>\n";
  	      }

  	    }

  	  }
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
    $this->data['table']  = "CRM_selectievelden";
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
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('module',
													array("description"=>"module",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('waarde',
													array("description"=>"waarde",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('omschrijving',
													array("description"=>"omschrijving",
													"default_value"=>"",
													"db_size"=>"120",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"80",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('extra',
													array("description"=>"extra",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));
                          
	 if($_GET['key_waarde'])
	   $this->data['fields']['waarde']['key_field'] = true;
	 else
	   $this->data['fields']['omschrijving']['key_field'] = true;

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>false,
													"list_visible"=>true,
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
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>