<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 juli 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/11/02 15:13:33 $
    File Versie         : $Revision: 1.24 $
 		
    $Log: GeconsolideerdePortefeuilles.php,v $
    Revision 1.24  2019/11/02 15:13:33  rvv
    *** empty log message ***

    Revision 1.23  2018/12/07 06:13:42  rvv
    *** empty log message ***

    Revision 1.22  2018/10/13 17:15:10  rvv
    *** empty log message ***

    Revision 1.21  2018/09/03 06:04:34  rvv
    *** empty log message ***

    Revision 1.20  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.19  2018/07/14 13:58:15  rvv
    *** empty log message ***

    Revision 1.18  2018/01/15 10:39:40  rvv
    *** empty log message ***

    Revision 1.17  2017/02/22 17:11:43  rvv
    *** empty log message ***

    Revision 1.16  2016/12/21 16:29:00  rvv
    *** empty log message ***

    Revision 1.15  2016/09/11 08:23:37  rvv
    *** empty log message ***

    Revision 1.14  2016/09/04 14:37:40  rvv
    *** empty log message ***

    Revision 1.13  2015/08/11 06:44:41  rvv
    *** empty log message ***

    Revision 1.12  2015/08/11 06:20:11  rvv
    *** empty log message ***

    Revision 1.11  2015/08/08 11:35:02  rvv
    *** empty log message ***

    Revision 1.10  2015/06/06 10:10:31  rvv
    *** empty log message ***

    Revision 1.9  2014/12/15 10:40:52  rvv
    *** empty log message ***

    Revision 1.8  2014/12/13 19:07:17  rvv
    *** empty log message ***

    Revision 1.7  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.6  2014/07/19 14:22:01  rvv
    *** empty log message ***

    Revision 1.5  2014/02/09 11:05:15  rvv
    *** empty log message ***

    Revision 1.4  2012/11/28 17:01:36  rvv
    *** empty log message ***

    Revision 1.3  2012/10/28 11:01:19  rvv
    *** empty log message ***

    Revision 1.2  2008/07/02 07:21:31  rvv
    *** empty log message ***

    Revision 1.1  2007/08/02 14:14:04  rvv
    *** empty log message ***

 		
 	
*/

class GeconsolideerdePortefeuilles extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function GeconsolideerdePortefeuilles()
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
	  $DB = new DB();
	  		
	  if($this->get("id") == 0 && $this->data['fields']['id']['value'] == 0)
	     $new = true;
		else
			$new=false;
	  if ($this->get("id") == $this->data['fields']['id']['value'] && $this->get("id") >0)
	  {
	    $update = true;
	    $query  = "SELECT * FROM GeconsolideerdePortefeuilles WHERE id = '".$this->get("id")."' ";
	  	$DB->SQL($query);
		  $DB->Query();
		  $oldData = $DB->lookupRecord();
	  }
	  
	  ($this->get("VirtuelePortefeuille")=="")?$this->setError("VirtuelePortefeuille",vt("Mag niet leeg zijn!")):true;
		($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille = '".$this->get("VirtuelePortefeuille")."' ";
		$DB->SQL($query);
		$DB->Query();
		$records = $DB->records();
		
		if(($records == 1 && $new) || $records > 1 || ($records == 1 && $update && $oldData['VirtuelePortefeuille'] != $this->get("VirtuelePortefeuille")))
			$this->setError("VirtuelePortefeuille", vtb("%s bestaat al", array($this->get("VirtuelePortefeuille"))));
			
		if ($this->get('Portefeuille1') == '' && $this->get('Portefeuille2') == '' && $this->get('Portefeuille3') == '' && $this->get('Portefeuille4') == '')
		{
		  $this->setError("Portefeuille1",vt('Er moet minimaal 1 Portefeuille gekoppeld worden.'));
		}
    
    
    $query  = "SELECT id FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille = '".$this->get("VirtuelePortefeuille")."' ";
    ($DB->QRecords($query))?$this->setError("VirtuelePortefeuille",vt("Is al aanwezig in PortefeuillesGeconsolideerd.")):true;


		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
		if($type=='verzenden')
		{
			global $USR;
			$db=new DB();
			$query="SELECT MAX(Vermogensbeheerders.CrmTerugRapportage) as CrmTerugRapportage FROM Vermogensbeheerders
              Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
              WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
			$db->SQL($query);
			$db->Query();
			$data=$db->lookupRecord();
			if($data['CrmTerugRapportage'] > 0)
				return true;
		}
		return checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "GeconsolideerdePortefeuilles";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Vermogensbeheerder',
										array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('VirtuelePortefeuille',
													array("description"=>"Virtuele Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE consolidatie=1 ",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles"));
                          

           /*
    $this->addField('Risicoprofiel',
													array("description"=>"Risicoprofiel",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Risicoklassen"));                          

                          //"select_query"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles ORDER BY Portefeuille limit 1",
													//"select_query_ajax"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE Portefeuille='{value}'",
		$this->addField('Client',
													array("description"=>"Client",
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

		$this->addField('Naam',
													array("description"=>"Naam",
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

		$this->addField('Naam1',
													array("description"=>"Naam1",
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

		$this->addField('SoortOvereenkomst',
										array("description"=>"SoortOvereenkomst",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT SoortOvereenkomst,SoortOvereenkomst FROM SoortOvereenkomsten ORDER BY SoortOvereenkomst",
													"form_size"=>"30",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"SoortOvereenkomsten",
													"categorie"=>"Gegevens"));
                          
		$this->addField('SpecifiekeIndex',
													array("description"=>"Specifieke Index",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Fonds FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
													'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"crm_readonly"=>true,
                          "keyIn"=>"Fondsen"));

		$this->addField('ModelPortefeuille',
													array("description"=>"ModelPortefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Portefeuille,Portefeuille FROM ModelPortefeuilles ORDER BY Portefeuille",
													"select_query_ajax"=>"SELECT Portefeuille, Portefeuille FROM ModelPortefeuilles WHERE Portefeuille='{value}'",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));
                          
		$this->addField('ZpMethode',
													array("description"=>"Zorplicht methode",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>'niet opgegeven',1=>'Via categorien',2=>'Via AFM standaarddeviatie'),
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Startdatum',
										array("description"=>"Startdatum",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		
 		$this->addField('Einddatum',
													array("description"=>"Einddatum",
													"db_size"=>"0",
													"db_type"=>"date",
													"default_value"=>"2037-12-31",
													"form_type"=>"calendar",
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
           */

		for($i=1;$i<41;$i++)
			$this->addField("Portefeuille$i",
											array("description"=>"Portefeuille $i",
														"default_value"=>"",
														"db_size"=>"24",
														"db_type"=>"varchar",
														"form_type"=>"selectKeyed",
														"form_options"=>array(''=>'Nog niet opgehaald.'),
														"form_size"=>"24",
														"form_visible"=>true,
														"list_visible"=>true,
														"list_width"=>"100",
														"list_align"=>"left",
														"list_search"=>false,
														"list_order"=>"true",
														"keyIn"=>"Portefeuilles"));
		
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