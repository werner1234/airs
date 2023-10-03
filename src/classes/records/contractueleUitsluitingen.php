<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 augustus 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/01/05 07:23:08 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: contractueleUitsluitingen.php,v $
    Revision 1.7  2020/01/05 07:23:08  rvv
    *** empty log message ***

    Revision 1.6  2018/03/11 10:51:19  rvv
    *** empty log message ***

    Revision 1.5  2017/12/23 18:11:43  rvv
    *** empty log message ***

    Revision 1.4  2017/10/22 11:13:47  rvv
    *** empty log message ***

    Revision 1.3  2017/09/13 15:49:12  rvv
    *** empty log message ***

    Revision 1.2  2017/08/19 18:12:17  rvv
    *** empty log message ***

    Revision 1.1  2017/08/16 15:55:21  rvv
    *** empty log message ***

 		
 	
*/

class ContractueleUitsluitingen extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ContractueleUitsluitingen()
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
/*
		if($this->get("categoriesoort")!="")
		{
			($this->get("categorie")=="")?$this->setError("categorie","Mag niet leeg zijn!"):true;
		}
*/
		if($this->get("categorie")!="" && $this->get("fonds")!="")
		{
			$this->setError("fonds",vt("Koppel een categoriesoort of een fonds. (Niet beiden.)"));
			$this->setError("categoriesoort",vt("Koppel een categoriesoort of een fonds. (Niet beiden.)"));
		}

    if($this->get("fonds") <> '' && $this->get("portefeuille") <> '')
		{
			$DB = new DB();
			$query = "SELECT id FROM contractueleUitsluitingen
	            WHERE
	            vermogensbeheerder   = '" . $this->get("vermogensbeheerder") . "' AND
	            Fonds                = '" . $this->get("fonds") . "' AND
              Portefeuille         = '" . $this->get("portefeuille") . "'";
			$DB->SQL($query);
			$DB->Query();
			$data = $DB->nextRecord();

			if ($DB->records() > 0 && $this->get("id") <> $data['id'])
			{
        if($this->get('soortReservering') <> 'Commitment')
        {
          $this->setError("vermogensbeheerder", vtb("%s bestaat al", array($this->get("vermogensbeheerder"))));
          $this->setError("fonds", vtb("%s bestaat al", array($this->get("fonds"))));
          $this->setError("portefeuille", vtb("%s bestaat al", array($this->get("portefeuille"))));
        }
			}
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
    $this->data['table']  = "contractueleUitsluitingen";
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

		$this->addField('vanaf',
													array("description"=>"Vanaf",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('einddatum',
										array("description"=>"Einddatum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
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
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ",
													"form_type"=>"selectKeyed",
													"form_extra"=>'onchange="selectieChanged();"',//getPortefeuilles(document.editForm.Vermogensbeheerder.value,'Portefeuilles','Portefeuille');
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('fonds',
										array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Fonds FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Fonds",
													'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
													"form_size"=>"25",
													"form_visible"=>true,
													"form_extra"=>'onchange=checkFonds($(this));',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('categoriesoort',
										array("description"=>"Categoriesoort",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_options"=>array('Beleggingscategorien'=>'Beleggingscategorien','Beleggingssectoren'=>'Beleggingssectoren','Fondssoort'=>'Fondssoort',
																								'Regios'=>'Regios','afmCategorien'=>'afmCategorien','Valuta'=>'Valuta','Rating'=>'Rating',
																								'Zorgplicht'=>'Zorgplichtcategorien','Hoofdcategorien'=>'Hoofdcategorien','Reservering'=>'Reservering' ),
													"form_size"=>"50",
													"form_visible"=>true,
													"form_extra"=>"onchange='javascript:selectieChanged();'",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('categorie',
										array("description"=>"Categorie",
													"default_value"=>"",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_size"=>"30",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													'keyCondition'=>'categoriesoort',
													'keyIn'=>'Beleggingscategorien,Beleggingssectoren,Regios,AttributieCategorien,afmCategorien,Rating,Zorgplichtcategorien'));
  
    $this->addField('portefeuille',
                    array("description"=>"Portefeuille",
                          "db_size"=>"24",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "form_size"=>"24",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "form_extra"=>"onchange='javascript:portefeuilleChanged();'",
                          "list_order"=>"true",
                          "keyIn"=>"Portefeuilles,GeconsolideerdePortefeuilles",
                          "crm_readonly"=>true));
  

    $this->addField('soortReservering',
                    array("description"=>"Soort reservering",
                          "default_value"=>"",
                          "db_size"=>"25",
                          "db_type"=>"varchar",
                          "form_type"=>"select",
                          "form_options"=>array('Garantie'=>'Garantie','Borgstelling'=>'Borgstelling','Minimum cash'=>'Minimum cash',
                                                'Overige reserveringen'=>'Overige reserveringen','Commitment'=>'Commitment'),
                          "form_size"=>"25",
                          "form_visible"=>true,
                        //  "form_extra"=>"onchange='javascript:soortReserveringChanged();'",
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    $this->addField('geldrekening',
                    array("description"=>"Geldrekening",
                          "default_value"=>"",
                          "db_size"=>"25",
                          "db_type"=>"varchar",
                          "form_type"=>"selectKeyed",
                          "form_size"=>"25",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          'keyIn'=>'Rekeningen'));
    $this->addField('bedrag',
                    array("description"=>"Bedrag",
                          "db_size"=>"25",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_format"=>"%01.2f",
                          "list_format"=>"%01.2f",
                          "form_size"=>"25",
                          "form_visible"=>true,
                          "list_visible"=>true,
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