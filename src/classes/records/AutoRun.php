<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 12 februari 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/30 12:44:27 $
    File Versie         : $Revision: 1.14 $
 		
    $Log: AutoRun.php,v $
    Revision 1.14  2020/05/30 12:44:27  rvv
    *** empty log message ***

    Revision 1.13  2018/11/16 16:35:56  rvv
    *** empty log message ***

    Revision 1.12  2018/11/08 07:50:22  rvv
    *** empty log message ***

    Revision 1.10  2018/11/07 17:04:39  rvv
    *** empty log message ***

    Revision 1.9  2018/03/14 17:11:20  rvv
    *** empty log message ***

    Revision 1.8  2017/10/14 17:18:04  rvv
    *** empty log message ***

    Revision 1.7  2017/08/02 18:20:54  rvv
    *** empty log message ***

    Revision 1.6  2017/07/30 10:23:11  rvv
    *** empty log message ***

    Revision 1.5  2017/07/09 07:29:38  rvv
    *** empty log message ***

    Revision 1.4  2017/07/08 17:14:42  rvv
    *** empty log message ***

    Revision 1.3  2017/05/24 15:54:34  rvv
    *** empty log message ***

    Revision 1.2  2012/12/02 11:02:59  rvv
    *** empty log message ***

    Revision 1.1  2008/02/14 08:58:32  rvv
    *** empty log message ***

 		
 	
*/

class AutoRun extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function AutoRun()
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

	  ($this->get("Rapportage")=="")?$this->setError("Rapportage",vt("Mag niet leeg zijn!")):true;
	  ($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
	  ($this->get("Trigger")=="")?$this->setError("Trigger",vt("Mag niet leeg zijn!")):true;

		/*
		$query  = "SELECT id FROM AutoRun WHERE Rapportage = '".$this->get("Rapportage")."' 
		                                                AND `Vermogensbeheerder` = '".$this->get("Vermogensbeheerder")."'
		                                                AND `Trigger` = '".$this->get("Trigger")."'  ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Rapportage",$this->get("Rapportage")." bestaat al bij '".$this->get("Vermogensbeheerder")."' met Trigger '".$this->get("Trigger")."' ");
		}
		*/

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
     return checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "AutoRun";
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

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ",
													"form_type"=>"selectKeyed",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rapportage',
													array("description"=>"Rapportage",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_options"=>array('Fondslijst','FondslijstKlein','Cashlijst','RapportAfmExport','RapportHSE_L63','RapportVOLK_L64','Modelcontrole','Zorgplichtcontrole','ouderdomsAnalyse','openFIXOrders','Mandaatcontrole','Mandaatcontrole_L79'),
												 	"form_extra"=>"onchange='javascript:checkRapportInstelling();'",
													"form_type"=>"select",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('Trigger',
													array("description"=>"Trigger",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_options"=>array('DataUpdate'),
													"form_type"=>"select",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
													
		$this->addField('BestandsNaam',
													array("description"=>"Bestands naam",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));		
													
		$this->addField('Export_pad',
													array("description"=>"Export pad",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('gebruikersnaam',
													array("description"=>"Gebruikersnaam",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 	$this->addField('wachtwoord',
													array("description"=>"Wachtwoord",
													"default_value"=>"",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('instellingen',
										array("description"=>"Instellingen",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"100",
													"form_rows"=>5,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('frequentie',
										array("description"=>"Frequentie",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>'',1=>'Dagelijks',2=>'Wekelijks',3=>'Maandelijks',4=>'Kwartaal',5=>'Jaarultimo',6=>'Ad-hoc'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('autoEmailadres',
										array("description"=>"Emailadres",
													"default_value"=>"",
													"db_size"=>"250",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('autoVanaf',
										array("description"=>"Automatisch vanaf",
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

		$this->addField('memo',
										array("description"=>"Memo",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"100",
													"form_rows"=>5,
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