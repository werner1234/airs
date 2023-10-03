<?php
/*
 		Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2018/08/27 09:15:42 $
 		File Versie					: $Revision: 1.10 $
*/

class Rekeningmutaties_v2 extends Table
{
  /*
  * Object vars
  */

  var $data = array();
  var $validationRules = array();
  /*
  * Constructor
  */
  function Rekeningmutaties()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}
  function addRequired ($field)
  {
    $this->addClass($field, 'requiredField');
  }
  
  /**
   * Set property of field
   * @param type $field
   * @param type $key
   * @param type $newValue
   */
  function setPropertie ($field, $key, $newValue)
  {
    $this->data['fields'][$field][$key] = $newValue;
  }
  function setvalue ($field, $newValue)
  {
    $this->setPropertie($field, 'value', $newValue);
  }
  
  function addClass ($field, $class)
  {//form_extra
    if ( ! isset ($this->data['fields'][$field]['form_class']) || empty ($this->data['fields'][$field]['form_class']) )
    {
      $this->data['fields'][$field]['form_class'] = $class;
    } else {
       $this->data['fields'][$field]['form_class'] .= ' '.$class;
    }
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

	/*
	 * Veldvalidatie
	 */
  function __checkValidationRules ($key) {
    if ( isset ($this->validationRules[$key]) && $this->validationRules[$key] == false ) {
      return false;
    }
    return true;
  }
  
	function validate()
	{
    if ( $_POST['mutation_type'] == 'geldtransacties' ) {
      if ( empty ($_POST["value"]) || $_POST["value"] == "0") {
        $this->addField('value',array("description"=>"Waarde"));
        $this->setError("value",vt("Waarde mag niet 0 zijn."));
      }
    }
    
		($this->get("Rekening")=="")?$this->setError("Rekening",vt("Mag niet leeg zijn!")):true;
		($this->get("Afschriftnummer")=="")?$this->setError("Afschriftnummer",vt("Mag niet leeg zijn!")):true;
		($this->get("Volgnummer")=="")?$this->setError("Volgnummer",vt("Mag niet leeg zijn!")):true;
		($this->get("Grootboekrekening")=="")?$this->setError("Grootboekrekening",vt("Mag niet leeg zijn!")):true;
    
    if ($this->__checkValidationRules('Omschrijving') === true) {($this->get("Omschrijving")=="")?$this->setError("Omschrijving",vt("Mag niet leeg zijn!")):true;}
    if ($this->__checkValidationRules('Fonds') === true) {($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;}
    
    $selectedFonds = $this->get('Fonds');
  
    if ($this->__checkValidationRules('Fonds') === true || ( $selectedFonds !== '' && $selectedFonds !== null) ) {
      $fonds = new Fonds();
      $selectedFonds = $fonds->parseBySearch(
        array('Fonds' => $this->get('Fonds'))
      );
      
      if ( $selectedFonds === false ) {
        $this->setError('Fonds', vt('Het geselecteerde fonds is niet bij ons bekend!'));
      }
    }
    
		($this->get("Valutakoers")=="")?$this->setError("Valutakoers",vt("Mag niet leeg zijn!")):true;
		($this->get("Grootboekrekening")=="FONDS" && $this->get("Transactietype") == '')?$this->setError("Transactietype",vt("Mag niet leeg zijn bij Fonds!")):true;

//    ( $this->get("Bedrag") == '0.0' || $this->get("Bedrag") == '' ) ? $this->setError("Bedrag","Mag niet leeg zijn!") : true;

	  if($this->get("Grootboekrekening")!="FONDS")
	  {
  	  if($this->get("Grootboekrekening")=="VERM")
	      $this->set("Transactietype",'B');
	    else
	      $this->set("Transactietype",'');
  	}
		// controle of datum <= afschrift datum ligt
		$query = " SELECT Datum FROM Rekeningafschriften WHERE ".
						 " Rekening = '".$this->get("Rekening")."' AND ".
						 " Afschriftnummer = '".$this->get("Afschriftnummer")."' AND".
						 " Datum >= '".$this->get("Boekdatum")."' AND YEAR(Datum) = YEAR('".$this->get("Boekdatum")."') ";
    
           $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		if($DB->Records() < 1)
		{
			$this->setError("Boekdatum",vt("Valt buiten afschriftdatum!"));
		}

		$DB = new DB();
		$DB->SQL("SELECT id FROM Rekeningmutaties WHERE Rekening = '".$this->get("Rekening")."' AND Afschriftnummer = '".$this->get("Afschriftnummer")."' AND Volgnummer = '".$this->get("Volgnummer")."' ");
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() > 0 && $this->get('id') != $data['id'])
		{
			$this->setError("Volgnummer",vt("combinatie rekening, afschriftnummer, volgnummer bestaat al"));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Rekeningmutaties";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rekening',
													array("description"=>"Rekening",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Rekeningen"));

		$this->addField('Afschriftnummer',
													array("description"=>"Afschriftnummer",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Volgnummer',
													array("description"=>"Volgnummer",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"17",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Boekdatum',
													array("description"=>"Boekdatum",
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

		$this->addField('Grootboekrekening',
													array("description"=>"Grootboekrekening",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_size"=>"5",
													"form_visible"=>true,
													"form_extra"=> '',//"onChange='javascript:grootboekChanged();'",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Grootboekrekeningen"));

		$this->addField('Valuta',
													array("description"=>"Valuta",
													"default_value"=>"EUR",
													"db_size"=>"4",
													"db_type"=>"char",
													"form_type"=>"select",
													"form_size"=>"4",
													"form_visible"=>true,
													"form_extra"=>"onBlur='javascript:valutaChanged();'",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Valutas"));

		$this->addField('Valutakoers',
													array("description"=>"Valutakoers",
													"default_value"=>"1.00",
													"db_size"=>"4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"form_extra"=>"onFocus=\"javascript:focusveld='Valutakoers';\" onBlur=\"javascript:focusveld='';\"",
													"form_format"=>"%01.10f",
													"list_format"=>"%01.10f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_extra"=>"onBlur='javascript:fondsChanged();'",
													"form_type"=>"selectKeyed",
													"form_size"=>"8",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('Aantal',
													array("description"=>"Aantal",
													"default_value"=>"",
													"db_size"=>"9",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
                          "form_format"=>"%01.6f",
                          "list_format"=>"%01.6f",
													"form_extra"=> '',//"onBlur='javascript:checkFondsAantal();'",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fondskoers',
													array("description"=>"Fondskoers",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"form_format"=>"%01.8f",
													"list_format"=>"%01.8f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Debet',
													array("description"=>"Debet",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
//													"form_extra"=>"onFocus=\"javascript:focusveld='Debet';\" onBlur=\"javascript:focusveld='';setBedrag('Debet');\"",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Credit',
													array("description"=>"Credit",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
//													"form_extra"=>"onFocus=\"javascript:focusveld='Credit';\"  onBlur=\"javascript:focusveld='';setBedrag('Credit');\"",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Bedrag',
													array("description"=>"Bedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Transactietype',
													array("description"=>"Transactietype",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Transactietypes"));

		$this->addField('Verwerkt',
													array("description"=>"Verwerkt",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"check",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Memoriaalboeking',
													array("description"=>"Memoriaalboeking",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"check",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Bewaarder',
													array("description"=>"Bewaarder",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Bewaarders"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
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
													"list_visible"=>true,
													"list_align"=>"right",
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
													"list_visible"=>true,
													"list_align"=>"right",
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
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('settlementDatum',
													array("description"=>"settlementDatum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
                          "form_class"=> "AIRSdatepicker",
                          "form_extra"=>" onchange=\"date_complete(this);\"",  
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bankTransactieId',
										array("description"=>"TransactieId",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	}
}
