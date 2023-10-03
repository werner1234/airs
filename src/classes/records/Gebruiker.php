<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/04/27 18:37:19 $
 		File Versie					: $Revision: 1.51 $

*/

class Gebruiker extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Gebruiker()
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
	  if($_SESSION['usersession']['gebruiker']['Gebruikersbeheer']==1)
		{
			if($this->get('Beheerder')==1 && $_SESSION['usersession']['gebruiker']['Beheerder']==0)
				return false;
			return true;
		}
    else 
     return checkAccess($type);
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
    global $__appvar;
		($this->get("Gebruiker")=="")?$this->setError("Gebruiker",vt("Mag niet leeg zijn!")):true;

		if(strpos($this->get("Gebruiker"),' ')!==false)
		  $this->setError("Gebruiker",vt("Spaties niet toegestaan."));
    
		$query  = "SELECT id, taal FROM Gebruikers WHERE Gebruiker = '".$this->get("Gebruiker")."' ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Gebruiker", vtb("%s bestaat al", array($this->get("Gebruiker"))));
		}

    // Bij het wijzigen van de taal
    if ( $this->get("taal") != $data['taal'] ) {
      $userName = $this->get("Gebruiker");
      $cache = new AE_cls_WidgetsCaching();
      $cache->deleteCacheForUser($userName);
    }

    if($this->get("participanten")==1)
    {
      $query  = "SELECT max(check_participants) as participanten FROM Vermogensbeheerders";
      $DB->SQL($query);
	  	$DB->Query();
	  	$data = $DB->nextRecord();
      if($data['participanten']==0)
        $this->setError("participanten","Geen vermogensbeheeder record met Participantenregister ingeschakeld gevonden.");
    }
    if($this->get("urenregistratie")>0)
    {
      $query  = "SELECT max(check_module_UREN) as uren FROM Vermogensbeheerders";
      $DB->SQL($query);
	  	$DB->Query();
	  	$data = $DB->nextRecord();
      if($data['uren']==0)
        $this->setError("urenregistratie","Geen vermogensbeheeder record met Urenregistratie ingeschakeld gevonden.");
    }    
    

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['table']  = "Gebruikers";
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

		$this->addField('Gebruiker',
													array("description"=>"Gebruiker",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"key_field"=>true));

		$this->addField('Naam',
													array("description"=>"Naam",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('titel',
													array("description"=>"Titel",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
	
		$this->addField('voornamen',
										array("description"=>"voornamen",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('achternaam',
										array("description"=>"achternaam",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('tussenvoegsel',
										array("description"=>"tussenvoegsel",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('taal',
                        array("description"=>"Taal",
                          "default_value"=>"",
                          "db_size"=>"200",
                          "db_type"=>"varchar",
                          "db_extra"=>"",
                          "form_type"=>"selectKeyed",
                          "form_options"=> $__appvar['vtTaal'],
                          "form_size"=>"15",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));


		$this->addField('paspoortNummer',
										array("description"=>"Paspoort nummer",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('geboortedatum',
										array("description"=>"Geboortedatum",
													"db_size"=>"50",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('paspoortGeldigTot',
										array("description"=>"Paspoort geldig tot",
													"db_size"=>"50",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('mobiel',
													array("description"=>"Mobiel nummer",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                    
		$this->addField('emailAdres',
													array("description"=>"email adres",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Wachtwoord',
													array("description"=>"Wachtwoord",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	  $this->addField('CRMlevel',
													array("description"=>"CRM level",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(-1=>'lezer',0=>"lezer (wel toevoegen)",1=>"gebruiker",2=>"beheerder"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			  $this->addField('bestandsvergoedingEdit',
													array("description"=>"Bestandsvergoeding",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>"lezer",1=>"bewerken"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

if($_SESSION['usersession']['gebruiker']['Gebruikersbeheer']==1 && $_SESSION['usersession']['gebruiker']['Beheerder']<1)
	  $this->addField('Beheerder',
													array("description"=>"Beheer",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(-1=>"Lezer",0=>"Gebruiker"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
else
  	  $this->addField('Beheerder',
													array("description"=>"Beheer",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(-1=>"Lezer",0=>"Gebruiker",1=>"Beheerder"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

			  $this->addField('beperkingOpheffen',
													array("description"=>"Toegang beperkte portefeuilles",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>"Uit",1=>"Aan"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


			  $this->addField('bgkleur',
													array("description"=>"Agenda kleur",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"form_type"=>"text",
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

		$this->addField('CRMeigenRecords',
													array("description"=>"CRM alleen eigen records",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('participanten',
													array("description"=>"Participantenregister",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

 		$this->addField('urenregistratie',
													array("description"=>"Rechten Urenreg.",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_select_option_notempty"=>true,
													"form_options"=>array(0=>"Geen rechten",1=>"Gebruiker",2=>"Beheerder"),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));  
                          
		$this->addField('Gebruikersbeheer',
													array("description"=>"Toegang tot gebruikersbeheer",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));
                          
		$this->addField('CRMxlsExport',
													array("description"=>"xls export uit",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('mutatiesAanleveren',
													array("description"=>"Rekeningmutaties aanleveren",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>"Uit",1=>"Aan",2=>"Alleen interne depots"),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('fondsmutatiesAanleveren',
													array("description"=>"Fondsmutaties aanleveren",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));
                          
 		$this->addField('fondsaanvragenAanleveren',
													array("description"=>"Fondsaanvragen aanleveren",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));
                                                
 		$this->addField('portefeuilledetailsAanleveren',
													array("description"=>"Portefeuilledetails aanleveren",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));                         

		$this->addField('verzendrechten',
													array("description"=>"Verzendrechten",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"default_value"=>0,
													"form_options"=>array(0=>"PDF",1=>"DDB",2=>"Email",3=>"DDB & Email"),
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('emailHandtekening',
													array("description"=>"Handtekening/body emails",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"mediumtext",
													"form_type"=>"htmlarea4",
													"form_rows"=>10,
													"form_size"=>50,
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('CRM_relatieSoorten',
													array("description"=>"CRM_relatieSoorten",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"mediumtext",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));                          

	 $this->addField('ordersNietAanmaken',
													array("description"=>"Orders aanmaken",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>"Toestaan",1=>"Niet toestaan"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

	 $this->addField('ordersNietVerwerken',
													array("description"=>"Orders verwerken",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>"Toestaan",1=>"Niet toestaan"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));

  $this->addField('orderdesk',
													array("description"=>"Orderdesk medewerker",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true")); 

  $this->addField('orderbeheerder',
													array("description"=>"Order beheerder",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('orderRechten',
										array("description"=>"Orderrechten",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"mediumtext",
													"form_type"=>"htmlarea4",
													"form_rows"=>10,
													"form_size"=>50,
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('emailRechten',
                    array("description"=>"Emails ontvangen",
                          "default_value"=>"",
                          "db_size"=>"0",
                          "db_type"=>"mediumtext",
                          "form_type"=>"htmlarea4",
                          "form_rows"=>10,
                          "form_size"=>50,
                          "form_size"=>"0",
                          "form_visible"=>false,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
    

		$this->addField('Accountmanager',
													array("description"=>"Gekoppelde accountmanager",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query'=>"SELECT Accountmanager,Accountmanager FROM Accountmanagers ORDER BY Accountmanager",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Accountmanagers"));

	 $this->addField('internePortefeuilles',
													array("description"=>"Toegang interne portefeuilles",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>"Niet toestaan",1=>"Toestaan"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true"));
                          
	 $this->addField('overigePortefeuilles',
													array("description"=>"Eigen/Alle portefeuilles",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>"Eigen portefeuilles",1=>"Alle portefeuilles"),
													"form_select_option_notempty"=>true,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>false,
													"list_order"=>"true")); 
                          
   $this->addField('updateInfoAan',
													array("description"=>"Update meldingen aan",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "default_value"=>1,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));
		
		$this->addField('crmImport',
													array("description"=>"CRM import toestaan",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
                          "default_value"=>1,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"center",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('rechtenExterneQueries',
										array("description"=>"Rechten Externe Queries",
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