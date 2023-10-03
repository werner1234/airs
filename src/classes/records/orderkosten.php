<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 30 maart 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/07/22 16:05:56 $
    File Versie         : $Revision: 1.20 $
 		
    $Log: orderkosten.php,v $
    Revision 1.20  2020/07/22 16:05:56  rvv
    *** empty log message ***

    Revision 1.19  2018/10/13 17:15:10  rvv
    *** empty log message ***

    Revision 1.18  2016/12/24 16:28:17  rvv
    *** empty log message ***

    Revision 1.17  2016/12/04 13:21:19  rvv
    *** empty log message ***

    Revision 1.16  2016/11/30 16:49:28  rvv
    *** empty log message ***

    Revision 1.15  2016/11/27 11:05:13  rvv
    *** empty log message ***

    Revision 1.14  2016/10/24 06:46:05  rvv
    *** empty log message ***

    Revision 1.13  2016/10/23 11:36:50  rvv
    *** empty log message ***

    Revision 1.12  2016/09/07 15:29:02  rvv
    *** empty log message ***

    Revision 1.11  2016/08/03 18:21:33  rvv
    *** empty log message ***

    Revision 1.10  2016/03/19 16:54:50  rvv
    *** empty log message ***

    Revision 1.9  2015/12/21 08:57:12  rvv
    *** empty log message ***

    Revision 1.8  2015/12/19 08:18:29  rvv
    *** empty log message ***

    Revision 1.7  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.6  2014/04/19 16:18:34  rvv
    *** empty log message ***

    Revision 1.5  2014/01/11 15:56:16  rvv
    *** empty log message ***

    Revision 1.4  2013/04/30 06:17:42  rvv
    *** empty log message ***

    Revision 1.3  2013/04/29 10:35:23  rvv
    *** empty log message ***

    Revision 1.2  2013/04/20 16:37:06  rvv
    *** empty log message ***

    Revision 1.1  2013/03/30 12:27:02  rvv
    *** empty log message ***

 		
 	
*/

class Orderkosten extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function Orderkosten()
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
		($this->get("vermogensbeheerder")=="")?$this->setError("vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		($this->get("fondssoort")=="keuze")?$this->setError("fondssoort",vt("Er moet een optie geselecteerd worden.")):true;

	  $query  = "SELECT id FROM orderkosten WHERE ".
							" fondssoort = '".$this->get("fondssoort")."' AND ".
              " beursregio = '".$this->get("beursregio")."' AND ".
              " portefeuille = '".$this->get("portefeuille")."' AND ".
			        " valuta       = '".$this->get("valuta")."' AND ".
              " transactievorm = '".$this->get("transactievorm")."' AND ".
							" vermogensbeheerder = '".$this->get("vermogensbeheerder")."'";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("vermogensbeheerder",vt("deze combinatie bestaat al.(id=%s)", array($data['id'])));
			$this->setError("fondssoort",vt("deze combinatie bestaat al"));
      $this->setError("beursregio",vt("deze combinatie bestaat al"));
      $this->setError("transactievorm",vt("deze combinatie bestaat al"));
			$this->setError("valuta",vt("deze combinatie bestaat al"));
		}
    
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
    $this->data['table']  = "orderkosten";
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
													"select_query"=>"SELECT Vermogensbeheerder, concat(Vermogensbeheerder,' - ',Naam) FROM Vermogensbeheerders order by Vermogensbeheerder",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"keyIn"=>"Vermogensbeheerders"));
                          
$this->addField('portefeuille',
													array("description"=>"Portefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Portefeuille, Portefeuille FROM Portefeuilles WHERE consolidatie=0 AND Einddatum > NOW() ORDER BY Portefeuille",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));
                          
		$this->addField('fondssoort',
													array("description"=>"Fondssoort",
													"default_value"=>"keuze",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"db_type"=>"varchar",
													"form_select_option_notempty"=>true,
													"select_query"=>"SELECT * FROM ( (SELECT 'keuze','---') UNION (SELECT '','Allemaal') UNION (SELECT fondssoort, fondssoort FROM Fondsen WHERE fondssoort <> '' GROUP BY fondssoort ORDER BY fondssoort) ) fondssoort",
													"form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"form_extra"=>"onchange='fondssoortChange();'",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('valuta',
										array("description"=>"Valuta",
													"default_value"=>"EUR",
													"db_size"=>"4",
													"db_type"=>"char",
													"select_query"=>"SELECT Valuta, Valuta FROM Valutas ORDER BY Valuta",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Valutas"));
		$this->addField('beursregio',
													array("description"=>"Beursregio",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"db_type"=>"varchar",
													"select_query"=>"SELECT beursregio, beursregio FROM Beurzen ORDER BY beurs",
													"form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
		$this->addField('transactievorm',
													array("description"=>"Transactievorm",
													"default_value"=>"",
													"db_size"=>"1",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "form_options"=>array('o'=>'openen','s'=>'sluiten'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                    
		$this->addField('kostenpercentage',
													array("description"=>"Kostenpercentage",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('kostenminimumbedrag',
													array("description"=>"Kostenminimumbedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('prijsPerStuk',
										array("description"=>"Transactiekosten per contract",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('brokerkostenpercentage',
													array("description"=>"Brokerkostenpercentage",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('brokerkostenminimumbedrag',
													array("description"=>"Brokerkostenminimumbedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('prijsPerStukBroker',
										array("description"=>"Brokerkosten per contract",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('berekenwijze',
										array("description"=>"Berekenwijze",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_select_option_notempty"=>true,
													"form_options"=>array(0=>'vast',1=>'staffel',2=>'schijven'),
													"form_extra"=>"onchange='berekenwijzeChange();'",
													"form_size"=>"8",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		for($i=1;$i<6;$i++)
		{
			$this->addField('staffel'.$i,
											array("description"=>"staffel".$i,
														"default_value"=>"",
														"db_size"=>"0",
														"db_type"=>"double",
														"form_type"=>"text",
														"form_size"=>"0",
														"form_visible"=>true,
														"list_visible"=>true,
														"list_format"=>"%01.2f",
														"list_width"=>"100",
														"list_align"=>"left",
														"list_search"=>false,
														"list_order"=>"true"));
			$this->addField('staffelPercentage'.$i,
											array("description"=>"staffelPercentage".$i,
														"default_value"=>"",
														"db_size"=>"0",
														"db_type"=>"double",
														"form_type"=>"text",
														"form_size"=>"0",
														"form_visible"=>true,
														"list_visible"=>true,
														"list_format"=>"%01.2f",
														"list_width"=>"100",
														"list_align"=>"left",
														"list_search"=>false,
														"list_order"=>"true"));
		}
  
    $valutas=array('CHF','GBP','USD');
    foreach($valutas as $valuta)
      $this->addField('factor'.$valuta,
                      array("description"=>"factor ".$valuta,
                            "default_value"=>"",
                            "db_size"=>"0",
                            "db_type"=>"double",
                            "form_type"=>"text",
                            "form_size"=>"0",
                            "form_visible"=>true,
                            "list_visible"=>true,
                            "list_format"=>"%01.6f",
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