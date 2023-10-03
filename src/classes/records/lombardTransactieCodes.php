<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/06/15 08:04:32 $
    File Versie         : $Revision: 1.8 $
 		
    $Log: lombardTransactieCodes.php,v $
    Revision 1.8  2018/06/15 08:04:32  cvs
    call 6572

    Revision 1.7  2018/06/15 07:43:11  cvs
    call 6063

    Revision 1.6  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.5  2017/09/20 06:09:56  cvs
    megaupdate 2722

    Revision 1.4  2017/04/03 12:10:19  cvs
    no message

    Revision 1.3  2017/02/08 14:27:24  cvs
    recommit

    Revision 1.2  2016/08/29 10:21:05  cvs
    option dropkeuzes aangepast

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class LombardTransactieCodes extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function LombardTransactieCodes()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
    $this->initModule();
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
		($this->get("LOMcode")=="")?$this->setError("LOMcode",vt("Mag niet leeg zijn!")):true;
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
    $tst->tableExist("lombardTransactieCodes",true);
    $tst->changeField("lombardTransactieCodes","LOMcode",array("Type"=>" varchar(25)","Null"=>false));
    $tst->changeField("lombardTransactieCodes","omschrijving",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField("lombardTransactieCodes","doActie",array("Type"=>" varchar(10)","Null"=>false));
  }

	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "Lombard transactiecodes";
    $this->data['table']  = "lombardTransactieCodes";
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

		$this->addField('LOMcode',
													array("description"=>"LOMcode",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
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
                                              "A"       =>  "A &nbsp;- Aankoop van stukken",
                                              "BEH"     => "BEH - Beheer",
                                						  "BEW"      => "BEW - Bewaarloon",
                                              "DIV"     => "DIV - Dividend",
                                              "FX"      => "FX - Valutatransactie",
                                              "KNBA"    => "KNBA - Bankkosten",
                                              "KRUIS"   => "KRUIS - Kruispost",
                                              "LOS"     => "LOS - Lossing",
                                              "MARMUT"  => "MARMUT - Margin mutaties ",
                                              "MUT"     => "MUT - Geld mutaties ",
                                              "R"       => "R - Rente",
                                              "RENOB"   => "RENOB - Coupon / Meeverk. Rente",
                                              "STUKMUT" => "STUKMUT - Stukken deponering/lichting",
                                              "V"       =>  "V &nbsp;- Verkoop van stukken",
                                              "VKSTO"   => "VKSTO - Verkoop stockjes",
                                              "NVT"     =>"N.v.t.",
													                     ""       => "------------------------------"),
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