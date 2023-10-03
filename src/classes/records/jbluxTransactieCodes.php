<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/29 11:49:18 $
    File Versie         : $Revision: 1.5 $
 		
    $Log: jbluxTransactieCodes.php,v $
    Revision 1.5  2020/06/29 11:49:18  cvs
    call 7829

    Revision 1.4  2020/04/10 13:07:52  cvs
    call 8554

    Revision 1.3  2020/03/09 13:29:39  cvs
    call 8413

    Revision 1.2  2020/02/24 15:28:20  cvs
    call 7829

    Revision 1.1  2020/01/27 10:57:20  cvs
    update 6-11-2019

    Revision 1.1  2019/10/04 07:41:59  cvs
    call 7598

    Revision 1.2  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.1  2017/09/20 06:09:01  cvs
    megaupdate 2722

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class jbluxTransactieCodes extends Table
{
  /*
  * Object vars
  */
  var $tableName = "jbluxTransactieCodes";
  var $data = array();
  
  /*
  * Constructor
  */
  function jbluxTransactieCodes()
  {
    $this->defineData();
    $this->initModule();
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
		($this->get("bankCode")=="")?$this->setError("bankCode",vt("Mag niet leeg zijn!")):true;
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
    $tst->tableExist("jbluxTransactieCodes",true);
    $tst->changeField("jbluxTransactieCodes","bankCode",array("Type"=>" varchar(20)","Null"=>false));
    $tst->changeField("jbluxTransactieCodes","omschrijving",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField("jbluxTransactieCodes","doActie",array("Type"=>" varchar(10)","Null"=>false));
  }

  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "JBlux transactiecodes";
    $this->data['table']  = $this->tableName;
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

		$this->addField('bankCode',
													array("description"=>"bankCode",
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
														"A" 				=>  "A &nbsp;- Aankoop van stukken",
														"BEH" 			=> "BEH - Beheer",
														"BEW" 			=> "BEW - Bewaarloon",
														"DIV" 			=> "DIV - Dividend",
														"EFF_KNBA" 	=> "EFF_KNBA - Effecten kosten",
														"EXPOPT" 		=> "EXPOPT - Expiratie opties",
														"FORW"  		=> "FORW - Forward",
														"FX" 				=> "FX - Forex",
														"KNBA" 			=> "KNBA - Bankkosten",
														"MUT"				=> "MUT - Geld mutaties",
														"OPTA" 			=> "OPTA - Aankoop opties",
														"OPTV" 			=> "OPTV - Verkoop opties",
														"RENOB" 		=> "RENOB - Coupon / Meeverk. Rente",
														"RENTE" 		=> "RENTE - Rente",
														"STUKMUT" 	=>  "STUKMUT - deponeringen/lichtingen stukken",
														"V" 				=>  "V &nbsp;- Verkoop van stukken",
                            "VERW" 			=> "VERW &nbsp;- Verschilling stukken",

                            "NVT" 			=>"N.v.t.",
														),
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
