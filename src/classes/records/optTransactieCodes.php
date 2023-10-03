<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/05/09 11:41:07 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: optTransactieCodes.php,v $
    Revision 1.2  2018/05/09 11:41:07  cvs
    call 6572

    Revision 1.1  2018/05/09 11:40:41  cvs
    call 6878

    Revision 1.4  2017/04/12 14:16:57  cvs
    call 5785

    Revision 1.3  2017/04/03 12:15:45  cvs
    call 5174

    Revision 1.2  2016/07/01 14:36:09  cvs
    call 5005

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class OptTransactieCodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function OptTransactieCodes()
  {
    $this->initModule();
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
		($this->get("OPTcode")=="")?$this->setError("OPTcode",vt("Mag niet leeg zijn!")):true;
		($this->get("omschrijving")=="")?$this->setError("omschrijving",vt("Mag niet leeg zijn!")):true;

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return true;
	}

  function initModule()
  {
    $tst = new SQLman();
    $tst->tableExist("optTransactieCodes",true);
    $tst->changeField("optTransactieCodes","OPTcode",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField("optTransactieCodes","omschrijving",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField("optTransactieCodes","doActie",array("Type"=>" varchar(10)","Null"=>false));
  }


  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "OPT transactiecodes";
    $this->data['table']  = "optTransactieCodes";
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
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('OPTcode',
													array("description"=>"OPTcode",
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

		$this->addField('doActie',
													array("description"=>"doActie",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options" => array( 
                                             "A"       => "A &nbsp;- Aankoop van stukken",
                                             "DV"      => "DV - Dividend",
                                             "KO"      => "KO - Diverse kosten",
                                             "OP"      => "OP - Opname",
                                             "RENCP"   => "RENCP - Coupon/rente",
                                             "ST"      => "ST - Storting",
                                             "V"       => "V &nbsp;- Verkoop van stukken",
                                             "NVT"     => "N.v.t.",
                                             ""        =>  "------------------------------",
																							"AO"      =>  "AO - Aankoop openen",
                                             "AS"      => "AS - Aankoop sluiten",
																							"ASS"     => "ASS - Assignment opties",
																							"E"       => "E - Emissie",
																							"EO"      => "EO - Expiratie Opties",
																							"KNBA"    => "KNBA - Kosten depotbank",
																							"LOS"     => "LOS - Lossing",
																							"MUT"     => "MUT - Geld/stukken mutaties",

																							"R"       => "R - Rente",
                                             "VO"      =>  "VO - Verkoop openen",
                                             "VS"      => "VS - Verkoop sluiten"),

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