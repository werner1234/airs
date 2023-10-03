<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/05 14:54:33 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: raboTransactieCodes.php,v $
    Revision 1.4  2020/06/05 14:54:33  cvs
    call 8208

    Revision 1.3  2020/05/25 13:36:53  cvs
    call 8431/8208

    Revision 1.2  2019/11/15 14:10:11  cvs
    call 8208

    Revision 1.1  2019/06/19 11:50:18  cvs
    call 7649

    Revision 1.2  2018/03/07 16:48:06  rvv
    *** empty log message ***

    Revision 1.1  2017/09/20 06:09:01  cvs
    megaupdate 2722

    Revision 1.1  2016/04/04 08:22:13  cvs
    call 4712

    Revision 1.1  2015/12/01 08:56:03  cvs
    update 2540, call 4352



 		
 	
*/

class raboTransactieCodes extends Table
{
  /*
  * Object vars
  */
  var $tableName = "raboTransactieCodes";
  var $data = array();
  
  /*
  * Constructor
  */
  function raboTransactieCodes()
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
    $tst->tableExist("raboTransactieCodes",true);
    $tst->changeField("raboTransactieCodes","bankCode",array("Type"=>" varchar(10)","Null"=>false));
    $tst->changeField("raboTransactieCodes","omschrijving",array("Type"=>" varchar(50)","Null"=>false));
    $tst->changeField("raboTransactieCodes","doActie",array("Type"=>" varchar(10)","Null"=>false));
  }

  /*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "RABO transactiecodes";
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
                                              "A" =>  "A &nbsp;- Aankoop van stukken",
                                              "AO" => "AO &nbsp;- Aankoop openen",
                                              "AS" => "AS &nbsp;- Aankoop sluiten",
                                              "BEH" => "BEH - Beheer",
                                              "BEW" => "BEW - Bewaarloon",
                                              "CA1" => "CA1 - Corp act",
                                              
                                              "DIV" => "DIV - Dividend",
                                              "KNBA" => "KNBA - Bankkosten",
                                              "KOBU" => "KOBU - Kosten buitenland",
                                              "MUT" => "MUT - Geld mutaties",
                                              "RENOB" => "RENOB - Coupon ",
                                              "RENTE" => "RENTE - Rente",
                                              "STUKMUT" => "STUKMUT - Stukken mutaties",
                                              "STUKMUT0" => "STUKMUT0 - Stukken mutaties zonder koers",
                                              "V" =>  "V &nbsp;- Verkoop van stukken",
                                              "VO" => "VO &nbsp;- Verkoop openen",
                                              "VS" => "VS &nbsp;- Verkoop sluiten",
                                              "NVT" =>"N.v.t.",
                                              "--" => "------------------------------",

                                              "FX" => "FX - Forex",
                                              "KRUIS" => "KRUIS - Kruispost",
                                              "RENME" => "RENME - Meegek. Rente",

                                              "VKSTO" => "VKSTO - Verkoop stockjes",
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




