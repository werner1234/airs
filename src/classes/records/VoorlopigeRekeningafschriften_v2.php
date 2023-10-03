<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2017/11/29 15:54:44 $
 		File Versie					: $Revision: 1.1 $

 		$Log: VoorlopigeRekeningafschriften_v2.php,v $
 		Revision 1.1  2017/11/29 15:54:44  rm
 		6331 - datum deze en vorige maand tonen
 		
 		Revision 1.8  2014/12/18 07:23:35  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/03/26 18:22:55  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/09/04 16:09:28  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2009/07/12 09:23:10  rvv
 		javascript weer teruggezet

 		Revision 1.4  2009/07/06 08:44:10  cvs
 		javascript aanroep verwijderd

 		Revision 1.3  2009/05/23 15:28:43  rvv
 		*** empty log message ***

 		Revision 1.2  2009/04/29 13:00:38  rvv
 		*** empty log message ***

 		Revision 1.1  2009/04/25 15:30:11  rvv
 		*** empty log message ***

 		Revision 1.11  2008/01/23 07:29:09  rvv
 		*** empty log message ***

 		Revision 1.10  2007/08/02 14:14:04  rvv
 		*** empty log message ***

 		Revision 1.9  2006/01/13 15:46:51  jwellner
 		diverse aanpassingen

 		Revision 1.8  2005/12/16 14:43:09  jwellner
 		classes aangepast

 		Revision 1.7  2005/10/26 11:47:39  jwellner
 		no message

 		Revision 1.6  2005/07/11 10:58:46  cvs
 		*** empty log message ***


*/

class VoorlopigeRekeningafschriften_v2 extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function VoorlopigeRekeningafschriften_v2()
  {
    $this->defineData();
    $this->set($this->data['identity'],0);
    $this->setDefaults();
    $this->error = false;
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{

		switch($type)
		{
			default :
				if($this->get("Verwerkt") == 1)
					return false;
			break;
		}
		return true;
//		return checkAccess($type);
	}

	function validate()
	{

		($this->get("Rekening")=="")?$this->setError("Rekening",vt("Mag niet leeg zijn!")):true;
		($this->get("Afschriftnummer")=="")?$this->setError("Afschriftnummer",vt("Mag niet leeg zijn!")):true;

		$DB = new DB();
		$DB->SQL("SELECT id FROM VoorlopigeRekeningafschriften WHERE Rekening = '".$this->get("Rekening")."' AND Afschriftnummer = '".$this->get("Afschriftnummer")."'");
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Rekening",vt("combinatie bestaat al"));
			$this->setError("Afschriftnummer",vt("combinatie bestaat al"));
		}

		//$this->get("Datum");

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	function validateDelete()
	{
		$DB = new DB();
		$SQL = "SELECT * FROM VoorlopigeRekeningmutaties WHERE Afschriftnummer = '". $this->get("Afschriftnummer")."' AND Rekening = '". $this->get("Rekening")."'";
		$recs = $DB->QRecords($SQL);
		if ($recs > 0)
		{
			$this->setError("Algemeen", vtb("Dit record mag niet worden verwijderd. Er zijn nog %s onderliggende mutaties aanwezig.", array($recs)));
		}
		$valid = ($this->error==false)?true:false;
		return $valid;
	}


	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "VoorlopigeRekeningafschriften";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rekening',
													array("description"=>"Rekening",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Rekeningen"));

		$this->addField('Afschriftnummer',
													array("description"=>"Afschriftnummer",
													"value"=>"test",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('Datum',
													array("description"=>"Datum",
																"default_value"=>"lastworkday",
																"db_size"=>"0",
																"db_type"=>"date",
																"form_type"=>"calendar",
																"form_class"=> "AIRSdatepicker AIRSdatepickerPreviousMonth",
																"form_extra"=>" onchange=\"date_complete(this);\"",
																"form_size"=>"8",
																"form_visible"=>true,
																"list_visible"=>true,
																"list_align"=>"right",
																"list_search"=>false,
																"list_order"=>"true"));

		$this->addField('Saldo',
													array("description"=>"Saldo",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('NieuwSaldo',
													array("description"=>"NieuwSaldo",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Verwerkt',
													array("description"=>"Verwerkt",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"form_size"=>"10",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>