<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 9 juli 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/07/18 15:41:32 $
    File Versie         : $Revision: 1.8 $
 		
    $Log: portefeuilleClusters.php,v $
    Revision 1.8  2020/07/18 15:41:32  rvv
    *** empty log message ***

    Revision 1.7  2018/02/18 08:45:38  rvv
    *** empty log message ***

    Revision 1.6  2018/02/17 19:13:54  rvv
    *** empty log message ***

    Revision 1.5  2017/12/23 18:11:43  rvv
    *** empty log message ***

    Revision 1.4  2017/10/01 14:38:05  rvv
    *** empty log message ***

    Revision 1.3  2017/09/17 14:58:12  rvv
    *** empty log message ***

    Revision 1.2  2017/07/12 15:58:23  rvv
    *** empty log message ***

    Revision 1.1  2017/07/09 11:58:29  rvv
    *** empty log message ***

 		
 	
*/

class PortefeuilleClusters extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function PortefeuilleClusters()
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
			$query  = "SELECT * FROM portefeuilleClusters WHERE id = '".$this->get("id")."' ";
			$DB->SQL($query);
			$DB->Query();
			$oldData = $DB->lookupRecord();
		}
		else
			$update=false;


		($this->get("vermogensbeheerder")=="")?$this->setError("vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("cluster")=="")?$this->setError("cluster",vt("Mag niet leeg zijn!")):true;


		$query  = "SELECT id FROM portefeuilleClusters WHERE cluster = '".$this->get("cluster")."' && vermogensbeheerder = '".$this->get("vermogensbeheerder")."' ";
		$DB->SQL($query);
		$DB->Query();
		$records = $DB->records();
//	echo $this->get("id")." <> ".$this->data['fields']['id']['value'];
		if(($records == 1 && $new) || $records > 1 || ($records == 1 && $update && $oldData['cluster'] != $this->get("cluster")))
			$this->setError("cluster",vtb("%s bestaat al voor %s", array($this->get("cluster"), $this->get("vermogensbeheerder"))));



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

	  return checkAccess();
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "portefeuilleClusters";
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

		$this->addField('vermogensbeheerder',
										array("description"=>"Vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder  FROM Vermogensbeheerders ",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('cluster',
													array("description"=>"Cluster",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('clusterOmschrijving',
													array("description"=>"ClusterOmschrijving",
													"default_value"=>"",
													"db_size"=>"75",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"75",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		for($i=1;$i<31;$i++)
			$this->addField("portefeuille$i",
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


		$this->addField('portaal',
											array("description"=>"Portaal",
														"db_size"=>"4",
														"db_type"=>"tinyint",
														"form_type"=>"checkbox",
														"form_visible"=>true,
														"list_visible"=>true,
														"list_align"=>"center",
														"list_search"=>false,
														"list_order"=>"true"));

		$this->addField('emailAdres',
										array("description"=>"Email adres",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('wachtwoord',
										array("description"=>"Wachtwoord",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('verzendAanhef',
										array("description"=>"verzendAanhef",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('actief',
										array("description"=>"Actief",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

  }
}
?>