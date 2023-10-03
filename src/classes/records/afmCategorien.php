<?php
/*
    AE-ICT CODEX source module versie 1.6, 14 december 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/08/30 11:40:56 $
    File Versie         : $Revision: 1.9 $

    $Log: afmCategorien.php,v $
    Revision 1.9  2015/08/30 11:40:56  rvv
    *** empty log message ***

    Revision 1.8  2015/08/26 15:44:37  rvv
    *** empty log message ***

    Revision 1.7  2015/06/14 13:59:06  rvv
    *** empty log message ***

    Revision 1.6  2015/06/14 13:55:20  rvv
    *** empty log message ***

    Revision 1.5  2015/06/14 13:48:19  rvv
    *** empty log message ***

    Revision 1.4  2015/05/17 09:27:49  rvv
    *** empty log message ***

    Revision 1.3  2015/05/16 09:33:38  rvv
    *** empty log message ***

    Revision 1.2  2011/12/21 19:16:40  rvv
    *** empty log message ***

    Revision 1.1  2011/12/14 19:32:50  rvv
    *** empty log message ***



*/

class AfmCategorien extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function AfmCategorien()
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
	  if($this->get("standaarddeviatie") < $this->get("standaarddeviatieMin"))
      $this->setError("standaarddeviatie",vtb("%s is < dan minimale waarde %s.", array($this->get("standaarddeviatie"), $this->get("standaarddeviatieMin"))));
	  elseif($this->get("standaarddeviatie") > $this->get("standaarddeviatieMax"))
      $this->setError("standaarddeviatie",vtb("%s is > dan maximale waarde %s.", array($this->get("standaarddeviatie"), $this->get("standaarddeviatieMax"))));
        
		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    global $__appvar;
    if($__appvar["bedrijf"] == "HOME")
	    return checkAccess();
    elseif($type=='edit')
      return true;  
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['name']  = "";
    $this->data['table']  = "afmCategorien";
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

		$this->addField('afmCategorie',
													array("description"=>"AFM categorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('omschrijving',
													array("description"=>"omschrijving",
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

		$this->addField('standaarddeviatie',
													array("description"=>"standaarddeviatie",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
                          "form_size"=>"5",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('standaarddeviatieMin',
													array("description"=>"standaarddeviatie Min",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"10",
                          "form_size"=>"5",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('standaarddeviatieMax',
													array("description"=>"standaarddeviatie Max",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"10",
                          "form_size"=>"5",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                  
		$this->addField('correlatie',
													array("description"=>"correlatie",
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