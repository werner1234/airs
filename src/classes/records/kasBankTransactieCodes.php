<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/06/17 08:29:43 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: kasBankTransactieCodes.php,v $
    Revision 1.4  2019/06/17 08:29:43  cvs
    call 7869

    Revision 1.3  2017/04/03 13:01:41  cvs
    call 5406

    Revision 1.2  2016/10/21 14:01:32  cvs
    call 5346

    Revision 1.1  2014/11/05 12:53:50  cvs
    dbs 2751

 		
 	
*/

class KasBankTransactieCodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function KasBankTransactieCodes()
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
		($this->get("kasbankCode")=="")?$this->setError("kasbankCode",vt("Mag niet leeg zijn!")):true;
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
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "Kasbank transactiecodes";
    $this->data['table']  = "kasBankTransactieCodes";
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

		$this->addField('kasbankCode',
													array("description"=>"kasbankCode",
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



    $doactieArray = array(
      "BEH"         => "BEH - Beheer",
      "BEW"         => "BEW - Bewaarloon",
      "DV"          => "DV -  Dividend",
      "KST"         => "KST - Kosten",
      "MUT"         => "MUT - Geld mutaties",
      "R"           => "R - Rente",
      "VMAR"        => "VMAR - variation margin ",
      "ZZ"          => "---- Stukken ----",
      "effStukmut"  => "S - stukken mutaties ",
      "NVT"         =>"N.v.t."
    );

		$this->addField('doActie',
													array("description"=>"doActie",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options" => $doactieArray,
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('actieAlternatief',
                    array("description"=>"alternatieve actie",
                          "default_value"=>"",
                          "db_size"=>"10",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "form_options" => $doactieArray,
                          "form_size"=>"10",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"center",
                          "list_search"=>false,
                          "list_order"=>"true"));

    $this->addField('portefeuillesAltActies',
                    array("description"=>"portefeuillesAltActies",
                          "default_value"=>"",
                          "db_size"=>"50",
                          "db_type"=>"varchar",
                          "form_type"=>"textarea",
                          "form_rows" => 4,
                          "form_size"=>"50",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));

  }
}
?>