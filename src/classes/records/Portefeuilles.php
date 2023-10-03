<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/20 17:07:11 $
 		File Versie					: $Revision: 1.102 $
*/

class Portefeuilles extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Portefeuilles()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
	  global $__appvar,$USR;
   	if($_SESSION['usersession']['superuser'])
    {
      if(isset($__appvar["homeAdmins"]) && $type=='delete')
      {
        if(in_array($USR,$__appvar["homeAdmins"]))
          return true;
      }
      else
		    return true;
	  }
    return false;
	}

	function validate()
	{
		($this->get("Portefeuille")=="")?$this->setError("Portefeuille",vt("Mag niet leeg zijn!")):true;
    ($this->get("Depotbank")=="")?$this->setError("Depotbank",vt("Mag niet leeg zijn!")):true;
    ($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
		$query  = "SELECT id FROM Portefeuilles WHERE Portefeuille = '".$this->get("Portefeuille")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Portefeuille",$this->get("Portefeuille")." bestaat al");
		}
    
    if(preg_replace("/[^A-Z0-9-_ \.]/i", "", $this->get("Portefeuille")) != $this->get("Portefeuille"))
    {
      $this->setError("Portefeuille",vtb("%s bevat ongewenste tekens.", array($this->get("Portefeuille"))));
    }

		$selectedClient = $this->get("Client");
		if ( ! empty ($selectedClient) ) {
			$clientObj = new Client();
			$clientData = $clientObj->parseBySearch(array('Client' => $selectedClient));
			if ( empty ($clientData) ) {
				$this->setError("Client",vt('Client Onbekend!'));
			}
		}
		else
    {
      $this->setError("Client",vt('Mag niet leeg zijn!'));
    }
/*
		$query  = "SELECT id FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille = '".$this->get("Portefeuille")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Portefeuille",$this->get("Portefeuille")." bestaat al als VirtuelePortefeuille.");
		}
*/
		$rapportageValuta=$this->get('RapportageValuta');
		if($rapportageValuta <> '' && $rapportageValuta <> 'EUR')
		{
			$query = "SELECT vvRappToegestaan FROM Vermogensbeheerders WHERE Vermogensbeheerder='" . $this->get('Vermogensbeheerder') . "'";
			$DB->SQL($query);
			$DB->Query();
			$data = $DB->nextRecord();
			if($data['vvRappToegestaan']==0)
			{
				$this->set('RapportageValuta', 'EUR');
				$this->setError("RapportageValuta",vt("RapportageValuta anders dan EUR niet geactiveerd."));
			}
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	function getCustomFields()
	{
	  foreach ($this->data['fields'] as $name=>$eigenschappen)
	  {
	    if($eigenschappen['form_visible'] == true)
	      $tmp[$name] = substr($eigenschappen['description'],0,10);
	  }
	  return $tmp;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['table']  = "Portefeuilles";
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
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"key_field"=>true,
													"crm_readonly"=>true,
													"categorie"=>"Gegevens"));
                          
if($__appvar["bedrijf"] == "HOME" || checkAccess())
{
  $this->addField('PortefeuilleDepotbank',
                  array("description"  => "Portefeuille depotbank",
                        "db_size"      => "24",
                        "db_type"      => "varchar",
                        "form_type"    => "text",
                        "form_size"    => "24",
                        "form_visible" => true, "list_width" => "150",
                        "list_visible" => true,
                        "list_align"   => "left",
                        "list_search"  => false,
                        "list_order"   => "true",
                        "categorie"    => "Gegevens"));
}
else
{
  $this->addField('PortefeuilleDepotbank',
                  array("description"  => "Portefeuille depotbank",
                        "db_size"      => "24",
                        "db_type"      => "varchar",
                        "form_type"    => "text",
                        "form_size"    => "24",
                        "form_visible" => true, "list_width" => "150",
                        "list_visible" => true,
                        "crm_readonly" => true,
                        "list_align"   => "left",
                        "list_search"  => false,
                        "list_order"   => "true",
                        "categorie"    => "Gegevens"));
}
                          
		$this->addField('PortefeuilleVoorzet',
													array("description"=>"PortefeuilleVoorzet",
													"db_size"=>"8",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('Vermogensbeheerder',
													array("description"=>"Vermogensbeheerder",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder ",
													"form_type"=>"selectKeyed",
													"form_extra"=>" onChange=\"javascript:vermogensbeheerderChanged();\" ",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders",
													"categorie"=>"Gegevens"));

		$this->addField('Client',
													array("description"=>"Client",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Client, Client FROM Clienten ORDER BY Client",
													"select_query_ajax"=>"SELECT Client, Client FROM Clienten WHERE Client='{value}'",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Clienten",
													//"crm_readonly"=>true,
													"categorie"=>"Gegevens"));

		$this->addField('Depotbank',
													array("description"=>"Depotbank",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Depotbank, Depotbank FROM Depotbanken ORDER BY Depotbank",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Depotbanken",
													"crm_readonly"=>true,
													"categorie"=>"Gegevens"));

		$this->addField('Vastetegenrekening',
													array("description"=>"Vastetegenrekening",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"26",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('Startdatum',
													array("description"=>"Startdatum",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"crm_readonly"=>true,
													"categorie"=>"Gegevens"));

		$this->addField('Einddatum',
													array("description"=>"Einddatum",
													"db_size"=>"0",
													"db_type"=>"date",
													"default_value"=>"2037-12-31",
													"form_type"=>"calendar",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));
  
    $this->addField('startdatumMeerjarenrendement',
                    array("description"=>"Start perf. meerjarenrend",
                          "db_size"=>"0",
                          "db_type"=>"date",
                          "form_type"=>"calendar",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Gegevens"));
    $this->addField('AfwStartdatumRend',
                    array("description"=>"Afwijkende Startdatum Rendement",
                          "db_size"=>"0",
                          "db_type"=>"date",
                          "form_type"=>"calendar",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Gegevens"));

		$this->addField('ClientVermogensbeheerder',
													array("description"=>"Client vermogensbeheerder",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_size"=>"20",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													//"crm_readonly"=>true,
													"categorie"=>"Gegevens"));

		$this->addField('AEXVergelijking',
													array("description"=>"Index-Vergelijking",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"crm_readonly"=>false,
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
													"categorie"=>"Gegevens",
                          "keyIn"=>"Fondsen"));

		//SpecifiekeIndex
		$this->addField('Accountmanager',
													array("description"=>"Accountmanager",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Accountmanagers",
													"categorie"=>"Gegevens"));

			$this->addField('tweedeAanspreekpunt',
													array("description"=>"Tweede aanspreekpunt",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Accountmanagers",
													"categorie"=>"Gegevens"));
/*
		$this->addField('Risicoprofiel',
													array("description"=>"Risicoprofiel",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));
*/
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

		$this->addField('HistorischeInfo',
													array("description"=>"HistorischeInfo",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('kwartaalAfdrukken',
													array("description"=>"Aantal kwartaal afdrukken",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('maandAfdrukken',
													array("description"=>"Aantal maand afdrukken",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('Risicoklasse',
													array("description"=>"Risicoprofiel",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Risicoklassen",
													"categorie"=>"Gegevens"));

		$this->addField('Taal',
													array("description"=>"Taal",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('BeheerfeeBasisberekening',
													array("description"=>"Basisbedrag berekening",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_options"=>$__appvar["BeheerfeeBasisberekening"],
                          "form_select_option_notempty"=>true,
													"form_size"=>"4",
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeMethode',
													array("description"=>"Methode",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeKortingspercentage',
													array("description"=>"Kortingspercentage",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeePercentageVermogen',
													array("description"=>"% Vermogen",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeePerformanceDrempelPercentage',
													array("description"=>"Performance drempel percentage",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeePerformanceViaHighwatermark',
													array("description"=>"Performance via highwatermark",
													"db_size"=>"0",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"form_extra"=>'',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeHighwatermarkStart',
													array("description"=>"Highwatermark start portefeuille",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeHighwatermarkOnder',
													array("description"=>"Tekort highwatermark bij start",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));
                          
		$this->addField('BeheerfeeBedrag',
													array("description"=>"Beheerfee vast bedrag",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);if(document.editForm.BeheerfeeBedrag.value > 0 && document.editForm.BeheerfeeMethode[0].checked){document.editForm.BeheerfeeMethode[3].checked=true;}"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeBedragBuitenBTW',
										array("description"=>"Beheerfee bedrag buiten BTW",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeBedragVast',
													array("description"=>"Beheerfee vast bedrag",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"crm_readonly"=>true,
													"categorie"=>"Beheerfee"));
                          
		$this->addField('BeheerfeePerformancePercentage',
													array("description"=>"Performance fee (%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeTeruggaveHuisfondsenPercentage',
													array("description"=>"Teruggave huisfondsen(%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeRemisiervergoedingsPercentage',
													array("description"=>"Remisier vergoeding (%)",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeAdministratieVergoeding',
													array("description"=>"Administratievergoeding",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));
                          
 	$this->addField('BeheerfeeAdminVgConUitsluiten',
													array("description"=>"Uitsluiten in consolidatie",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));
    $this->addField('BeheerfeeAdminVergoedingJaarlijks',
                    array("description"=>"Administratievergoeding jaarlijks",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,"list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Beheerfee"));
  
    $this->addField('BeheerfeeToevoegenAanPortefeuille',
													array("description"=>"Toevoegen aan Portefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles ORDER BY Portefeuille",
													"select_query_ajax"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE Portefeuille='{value}'",
													"form_type"=>"selectKeyed",
													"form_size"=>"24",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeAantalFacturen',
													array("description"=>"Aantal facturen beheerfee per jaar ",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"text",
													"form_size"=>"4",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

	$this->addField('BeheerfeePerformancefeeJaarlijks',
													array("description"=>"Performancefee jaarlijks berekenen ",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));


		$this->addField('BeheerfeeSchijvenTarief',
													array("description"=>"Schijven tarief hanteren.",
													"db_size"=>"0",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffel1',
													array("description"=>"Staffel 1",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffel2',
													array("description"=>"Staffel 2",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffel3',
													array("description"=>"Staffel 3",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffel4',
													array("description"=>"Staffel 4",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffel5',
													array("description"=>"Staffel 5",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffelPercentage1',
													array("description"=>"Staffel 1 %",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffelPercentage2',
													array("description"=>"Staffel 2 %",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffelPercentage3',
													array("description"=>"Staffel 3 %",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffelPercentage4',
													array("description"=>"Staffel 4 %",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeStaffelPercentage5',
													array("description"=>"Staffel 5 %",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Staffels"));

		$this->addField('BeheerfeeBTW',
													array("description"=>"BTW Percentage",
													"db_size"=>"5",
													"db_type"=>"decimal",
													"default_value"=>"21",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);checkBTW();"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('afwijkendeOmzetsoort',
										array("description"=>"Afwijkende omzetsoort",
													"db_size"=>"4",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_options"=>array('ICP'=>vt('Intracommunautaire dienst'),'EXP'=>vt('Export buiten de EU'),'VRIJ'=>vt('Vrijgestelde prestatie')),
													"form_size"=>"4",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeTransactiefeeKosten',
													array("description"=>"Kosten per transactie",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));                          
 
		$this->addField('Remisier',
													array("description"=>"Remisier",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('AFMprofiel',
													array("description"=>"AFMprofiel",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

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
													"keyIn"=>"Portefeuilles",
													"categorie"=>"Gegevens"));

		$this->addField('RapportageValuta',
													array("description"=>"RapportageValuta",
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
													"keyIn"=>"Valutas",
													"categorie"=>"Gegevens"));

		$this->addField('InternDepot',
													array("description"=>"Intern Depot",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('OptieToestaan',
													array("description"=>"Opties toestaan",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('BeheerfeeMinJaarBedrag',
													array("description"=>"Minimum jaar bedrag",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('WerkelijkeDagen',
													array("description"=>"Werkelijke dagen telling",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));


		$this->addField('valutaUitsluiten',
													array("description"=>"Liquiditeiten uitsluiten in feeberekening",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeFacturatieVanaf',
													array("description"=>"Facturatie vanaf",
													"db_size"=>"1",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeFacturatieVooraf',
													array("description"=>"Facturatie vooraf",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('BeheerfeeBedragBuitenFee',
													array("description"=>"Bedrag buiten beheerfee",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

		$this->addField('afrekenvalutaKosten',
													array("description"=>"Afrekenvaluta kosten",
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
													"keyIn"=>"Valutas",
													"categorie"=>"Beheerfee"));
                          
		$this->addField('beperktToegankelijk',
													array("description"=>"Beperkt toegankelijk",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));



				$this->addField('Memo',
													array("description"=>"Memo",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"50",
													"form_rows"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

			$this->addField('Aanbrenger',
													array("description"=>"Aanbrenger",
													"db_size"=>"200",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

				$this->addField('BetalingsinfoMee',
													array("description"=>"Betalingsinfo mee",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

			$this->addField('FactuurMemo',
													array("description"=>"Factuur memo",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"50",
													"form_rows"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

				$this->addField('BeheerfeeLiquiditeitenViaModel',
													array("description"=>"Liquiditeiten herrekenen via model",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

				$this->addField('BestandsvergoedingUitkeren',
													array("description"=>"Bestandsvergoeding uitkeren",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('feeToevoegMethode',
													array("description"=>"Toevoeg methode",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_select_option_notempty"=>true,
													"form_options"=>array(0=>vt('Volledig'),1=>vt('Verdeling naar vermogen'),2=>vt('Korting toebedelen')),
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));

			$this->addField('kleurcode',
													array("description"=>"Kleurcode",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"50",
													"form_rows"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));
                          
  		$this->addField('BeheerfeeHuisfondsenOvernemen',
													array("description"=>"Fee-berekening huisfonds overnemen",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"crm_readonly"=>true,
													"categorie"=>"Beheerfee"));
          
  		$this->addField('BeheerfeeLiquiditeitenAnderPercentage',
													array("description"=>"Liquiditeiten alternatief percentage",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));
                          
    		$this->addField('BeheerfeeLiquiditeitenPercentage',
													array("description"=>"Liquiditeiten percentage",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
                          "form_size"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee")); 

    		$this->addField('BeheerfeeLiquiditeitenAfroomPercentage',
													array("description"=>"Liquiditeiten afromen naar percentage",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
                          "form_size"=>"4",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Beheerfee"));                           
                          
                          
  		$this->addField('TijdelijkUitsluitenZp',
													array("description"=>"Tijdelijk uitsluiten zorgplichtcontrole",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_options"=>array(0=>'Niet uitsluiten',1=>'Geheel uitsluiten',2=>'Tijdelijk akkoord'),
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));
                          
		$this->addField('ZpMethode',
													array("description"=>"Zorplicht methode",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>'niet opgegeven',1=>'Via categorien',2=>'Via AFM standaarddeviatie',3=>'Via werkelijke standaarddeviatie'),
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('spreadKosten',
										array("description"=>"Spreadkosten in basispunten",
													"db_size"=>"11",
													"default_value"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));

		$this->addField('overgangsdepot',
										array("description"=>"Overgangsdepot",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"crm_readonly"=>true,
													"categorie"=>"Gegevens"));

		$this->addField('consolidatie',
										array("description"=>"Consolidatie",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
                          "form_extra"=>"onclick=\"checkVasteStart();\";",
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
                          "crm_readonly"=>true,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));
  
    $this->addField('consolidatieVasteStart',
                    array("description"=>"Consolidatie vaste startdatum",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Gegevens"));
    
    $this->addField('consolidatieVasteEind',
                    array("description"=>"Consolidatie vaste einddatum",
                          "db_size"=>"4",
                          "db_type"=>"tinyint",
                          "form_type"=>"checkbox",
                          "form_size"=>"4",
                          "form_visible"=>true,
                          "list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Gegevens"));
  
    $this->addField('selectieveld1',
                    array("description"=>"Selectieveld1",
                          "db_size"=>"40",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"40",
                          "form_visible"=>true,
                          "list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Gegevens"));
    $this->addField('selectieveld2',
                    array("description"=>"Selectieveld2",
                          "db_size"=>"40",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"40",
                          "form_visible"=>true,
                          "list_width"=>"150",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true",
                          "categorie"=>"Gegevens"));
  
  }
}
?>