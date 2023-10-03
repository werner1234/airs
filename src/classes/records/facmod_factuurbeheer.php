<?php
/*
    AE-ICT CODEX source module versie 1.2, 25 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/18 09:12:17 $
    File Versie         : $Revision: 1.2 $

    $Log: facmod_factuurbeheer.php,v $
    Revision 1.2  2019/11/18 09:12:17  cvs
    call 7675

    Revision 1.1  2019/07/22 09:11:00  cvs
    call 7675

    Revision 1.5  2011/05/31 08:04:50  cvs
    vervaldatum op factuur

    Revision 1.4  2008/01/23 12:06:05  cvs
    diverse kleine bugs en aanpassingen

    Revision 1.3  2006/11/10 08:32:02  cvs
    *** empty log message ***

    Revision 1.2  2005/12/06 08:32:23  cvs
    *** empty log message ***

    Revision 1.1  2005/11/28 07:31:48  cvs
    *** empty log message ***



*/

class facmod_factuurbeheer extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function facmod_factuurbeheer()
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
	  return true;
//	  $level = getMyLevel("Financieel");
	  switch ($type) 
	  {
		case "edit":
	  		return ($level >=3 )?true:false;
	  		break;
	  case "delete":
	  		return false;
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
    $this->data['name']  = "Verkoop facturen";
    $this->data['table']  = "facmod_factuurbeheer";
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

		$this->addField('voorzet',
													array("description"=>"voorzet",
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
													"list_order"=>"true"));

		$this->addField('facnr',
													array("description"=>"facnr",
													"default_value"=>"",
													"db_size"=>"12,0",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"12,0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('datum',
													array("description"=>"datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_extra"=>" ",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('vervaldatum',
													array("description"=>"vervaldatum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_extra"=>" ",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
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

		$this->addField('deb_id',
													array("description"=>"debnr",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('status',
													array("description"=>"status",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_options"=>array(),
													"form_select_option_notempty"=>true,
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('firmanaam',
													array("description"=>"firmanaam",
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

		$this->addField('bedrag_ex_h',
													array("description"=>"bedrag_ex_h",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('btw_h',
													array("description"=>"btw_h",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bedrag_ex_l',
													array("description"=>"bedrag_ex_l",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('btw_l',
													array("description"=>"btw_l",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bedrag_0',
													array("description"=>"bedrag_0",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



		$this->addField('bedrag_vl',
													array("description"=>"bedrag_vl",
													"default_value"=>"",
													"db_size"=>"9,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"9,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bedrag_vh',
													array("description"=>"bedrag_vh",
													"default_value"=>"",
													"db_size"=>"9,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"9,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bedrag_incl',
													array("description"=>"bedrag_incl",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('deelbetaling_1',
													array("description"=>"deelbetaling_1",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('deelbetaling_1_datum',
													array("description"=>"deelbetaling_1_datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
                                "form_class"=>"AIRSdatepicker",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('deelbetaling_2',
													array("description"=>"deelbetaling_2",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('deelbetaling_2_datum',
													array("description"=>"deelbetaling_2_datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_class"=>"AIRSdatepicker",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('deelbetaling_3',
													array("description"=>"deelbetaling_3",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('deelbetaling_3_datum',
													array("description"=>"deelbetaling_3_datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
                          "form_class"=>"AIRSdatepicker",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bedrag_voldaan',
													array("description"=>"bedrag_voldaan",
													"default_value"=>"",
													"db_size"=>"10,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"10,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('betaal_datum',
													array("description"=>"betaal_datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
                          "form_class"=>"AIRSdatepicker",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('betaal_dagen',
													array("description"=>"betaal_dagen",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('betalingstermijn',
													array("description"=>"betalingstermijn",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('door',
													array("description"=>"door",
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

		$this->addField('voetnoot',
													array("description"=>"voetnoot",
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

		$this->addField('memo',
													array("description"=>"memo",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_rows"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('factuurEmailLog',
                    array("description"=>"log",
                          "default_value"=>"",
                          "db_size"=>"60",
                          "db_type"=>"text",
                          "form_type"=>"textarea",
                          "form_size"=>"60",
                          "form_rows"=>"6",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));


    $this->addField('incasso',
													array("description"=>"incasso",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"char",
													"form_type"=>"checkbox",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('incassotxt',
													array("description"=>"incassotxt",
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

		$this->addField('korting',
													array("description"=>"korting",
													"default_value"=>"",
													"db_size"=>"4,2",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"4,2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rekeningnr',
													array("description"=>"rekeningnr",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('grekening',
													array("description"=>"grekening",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"1",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('grekening_percentage',
													array("description"=>"grekening_percentage",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
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
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
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
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
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
													"list_order"=>"true"));

    $this->addField('email_datum',
                    array("description"=>"E-mail datum",
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

    $this->addField('email_factuur',
                    array("description"=>"E-mail",
                          "default_value"=>"",
                          "db_size"=>"100",
                          "db_type"=>"text",
                          "form_type"=>"text",
                          "form_size"=>"60",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));



  }
}
?>