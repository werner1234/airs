<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/09/08 06:47:35 $
 		File Versie					: $Revision: 1.36 $

*/

class Rekeningen extends Table
{
  /*
  * Object vars
  */

  var $data = array(
//    'templatePath' =>  'Rekeningen'
  );

  /*
  * Constructor
  */
  function Rekeningen()
  {
    $this->defineData();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

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

	function validate()
	{
	  global $__appvar;
		($this->get("Rekening")=="")?$this->setError("Rekening",vt("Mag niet leeg zijn!")):true;
		($this->get("Portefeuille")=="")?$this->setError("Portefeuille",vt("Mag niet leeg zijn!")):true;
    ($this->get("Valuta")=="")?$this->setError("Valuta",vt("Mag niet leeg zijn!")):true;

		$checkPortefeuille = $this->get("Portefeuille");
		if ( ! empty($checkPortefeuille) ) {
			$portefeuilleObj = new Portefeuilles();
			$portefeuilleData = $portefeuilleObj->parseBySearch(array('Portefeuille' => $checkPortefeuille));

			if ( empty ($portefeuilleData) ) {
				$this->setError("Portefeuille",vt("Portefeuille onbekend!"));
			}
		}

		$query  = "SELECT id FROM Rekeningen WHERE Rekening = '".$this->get("Rekening")."' AND consolidatie=0 ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Rekening",vtb("%s bestaat al", array($this->get("Rekening"))));
		}
    
    if($__appvar["bedrijf"] == "HOME")
    {
      if($this->get("id")==0 && $this->get('Depotbank')=='')
        $this->setError("Depotbank",vt("Mag niet leeg zijn! (voor nieuwe records)"));
    }
    
    if(preg_replace("/[^A-Z0-9-_ \.]/i", "", $this->get("Rekening")) != $this->get("Rekening"))
    {
      $this->setError("Rekening", vtb("%s bevat ongewenste tekens.", array($this->get("Rekening"))));
    }

		// check op valuta bij termijnrekening
		if($this->get("Termijnrekening") > 0)
		{
			$query = "SELECT TermijnValuta FROM Valutas WHERE Valuta = '".$this->get("Valuta")."' ";
			$DB->SQL($query);
			$DB->Query();
			$data = $DB->nextRecord();
			($data[TermijnValuta]<1)?$this->setError("Valuta",vt("Valuta moet een Termijnvaluta zijn!")):true;
		}
    
    $query="SELECT check_rekeningATT,check_rekeningCat,check_rekeningDepotbank 
    FROM Vermogensbeheerders 
    JOIN Portefeuilles ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
    WHERE Portefeuille='".$this->get("Portefeuille")."' ";
    $DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
    if($data['check_rekeningATT']==1 && $this->get("AttributieCategorie")=="")
      $this->setError("AttributieCategorie",vtb("%s Mag niet leeg zijn!", array($this->get("AttributieCategorie"))));
    if($data['check_rekeningCat']==1 && $this->get("Beleggingscategorie")=="")
      $this->setError("Beleggingscategorie",vtb("%s Mag niet leeg zijn!", array($this->get("Beleggingscategorie"))));
    if($data['check_rekeningDepotbank']==1 && $this->get("Depotbank")=="")
      $this->setError("Depotbank",vtb("%s Mag niet leeg zijn!", array($this->get("Depotbank"))));
		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['table']  = "Rekeningen";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rekening',
													array("description"=>"Rekening",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"key_field"=>true));
                          
if($__appvar["bedrijf"] == "HOME" || checkAccess())
		$this->addField('RekeningDepotbank',
													array("description"=>"Rekening depotbank",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));
                          
		$this->addField('Memoriaal',
													array("description"=>"Memoriaal",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('Termijnrekening',
													array("description"=>"Termijnrekening",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>"SELECT Portefeuille, concat(Portefeuille,' - ',Vermogensbeheerder) FROM Portefeuilles ORDER BY Portefeuille",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));

		$this->addField('Valuta',
													array("description"=>"Valuta",
													"db_size"=>"4",
													"db_type"=>"char",
													"form_type"=>"select",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Valutas"));

		$this->addField('Tenaamstelling',
													array("description"=>"Tenaamstelling",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('IBANnr',
													array("description"=>"IBAN nr",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"30",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('Inleg',
													array("description"=>"Inleg",
													"db_size"=>"50",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
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

		$this->addField('RenteBerekenen',
													array("description"=>"Rente berekenen",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_extra"=>"onClick='javascript:showRente();'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rente30_360',
													array("description"=>"30/360 rente berekening",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Deposito',
													array("description"=>"Spaar/Deposito/Lening",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

			$this->addField('Beleggingscategorie',
													array("description"=>"Beleggingscategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Beleggingscategorie,Beleggingscategorie  FROM Beleggingscategorien ",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Beleggingscategorien"));

			$this->addField('AttributieCategorie',
													array("description"=>"AttributieCategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT AttributieCategorie ,AttributieCategorie  FROM AttributieCategorien ",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"AttributieCategorien"));

		$this->addField('Depotbank',
													array("description"=>"Depotbank",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Depotbank,Depotbank FROM Depotbanken ORDER BY Depotbank",
													"form_type"=>"selectKeyed",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Depotbanken"));

		$this->addField('Inactief',
													array("description"=>"Inactief",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
                          
    $this->addField('typeRekening',
													array("description"=>"Type Rekening",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		
		$this->addField('Afdrukvolgorde',
										array("description"=>"Afdrukvolgorde",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		                          
  }
}
?>