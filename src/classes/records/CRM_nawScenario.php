<?php
/*
    AE-ICT CODEX source module versie 1.2, 6 december 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/06/11 15:09:32 $
    File Versie         : $Revision: 1.3 $

    $Log: CRM_nawScenario.php,v $
    Revision 1.3  2017/06/11 15:09:32  rvv
    *** empty log message ***

    Revision 1.2  2014/09/13 14:39:56  rvv
    *** empty log message ***

    Revision 1.1  2014/05/29 12:14:50  rvv
    *** empty log message ***

    Revision 1.71  2014/02/28 16:38:50  rvv
    *** empty log message ***

    Revision 1.70  2014/02/22 18:38:01  rvv
    *** empty log message ***

    Revision 1.69  2014/01/22 16:57:54  rvv
    *** empty log message ***

    Revision 1.68  2014/01/21 13:52:41  cvs
    *** empty log message ***

    Revision 1.67  2014/01/18 17:22:27  rvv
    *** empty log message ***

    Revision 1.66  2013/12/18 17:01:59  rvv
    *** empty log message ***

    Revision 1.65  2013/12/16 11:00:56  rvv
    *** empty log message ***

    Revision 1.64  2013/12/14 17:13:55  rvv
    *** empty log message ***

    Revision 1.63  2013/11/16 16:05:47  rvv
    *** empty log message ***

    Revision 1.62  2013/09/28 14:40:56  rvv
    *** empty log message ***


*/

class CRM_nawScenario extends Table
{
  /*
  * Object vars
  */

  var $data = array();
  var $JaNee = Array("J"=>"Ja","N"=>"Nee");

  /*
  * Constructor
  */
  function CRM_nawScenario()
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
	}

	function getCustomFields()
	{

	}
	/*
  * Table definition
  */
  function defineData()
  {
    global $USR,$__appvar;
    $this->data['name']  = "relatie";
    $this->data['table']  = "CRM_naw";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;
		$db=new DB();

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));


		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('startvermogen',
													array("description"=>"startvermogen",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_format"=>"%01.0f","form_format"=>"%01.0f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"scenario"));
    $jaren=array();
    for($i=2010;$i<2100;$i++)
      $jaren[$i]=$i;
      
		$this->addField('startdatum',
													array("description"=>"startdatum",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options"=>$jaren,
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"scenario"));
                           
  $this->addField('doeldatum',
													array("description"=>"doeldatum",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options"=>$jaren,
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"scenario"));
                                                                             
 		$this->addField('doelvermogen',
													array("description"=>"doelvermogen",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_format"=>"%01.0f","form_format"=>"%01.0f",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"scenario"));

               if($db->QRecords("SELECT ScenarioGewenstProfiel FROM Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' AND ScenarioGewenstProfiel=1"))           
   $this->addField('gewenstRisicoprofiel',
													array("description"=>"Gewenst risicoprofiel",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Risicoklasse,Risicoklasse FROM Risicoklassen WHERE uitsluitenScenario=0 AND verwachtRendement <> 0 ",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"scenario"));    
                          
   $this->addField('maximaalRisicoprofiel',
													array("description"=>"Maximaal risicoprofiel",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Risicoklasse,Risicoklasse FROM Risicoklassen WHERE uitsluitenScenario=0 AND verwachtRendement <> 0 ",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"scenario")); 
                                                                                                                     
	
  }
}
?>