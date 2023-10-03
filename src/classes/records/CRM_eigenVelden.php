<?php
/*
    AE-ICT CODEX source module versie 1.6, 28 april 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/09/16 17:09:56 $
    File Versie         : $Revision: 1.13 $

    $Log: CRM_eigenVelden.php,v $
    Revision 1.13  2019/09/16 17:09:56  rvv
    *** empty log message ***

    Revision 1.12  2019/09/04 16:06:38  rvv
    *** empty log message ***

    Revision 1.11  2019/09/04 15:27:19  rvv
    *** empty log message ***

    Revision 1.10  2015/05/27 16:18:37  rvv
    *** empty log message ***

    Revision 1.9  2014/09/21 12:35:01  rvv
    *** empty log message ***

    Revision 1.8  2014/08/09 14:44:18  rvv
    *** empty log message ***

    Revision 1.7  2014/07/27 11:25:09  rvv
    *** empty log message ***

    Revision 1.6  2014/06/11 15:37:11  rvv
    *** empty log message ***

    Revision 1.5  2014/02/22 18:38:01  rvv
    *** empty log message ***

    Revision 1.4  2013/11/16 16:05:47  rvv
    *** empty log message ***

    Revision 1.3  2013/04/24 16:10:35  rvv
    *** empty log message ***

    Revision 1.2  2012/06/09 13:41:12  rvv
    *** empty log message ***

    Revision 1.1  2012/04/28 15:54:55  rvv
    *** empty log message ***



*/

class CRM_eigenVelden extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function CRM_eigenVelden()
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
	  $db=new DB();

	  if(ereg("[^A-Za-z0-9_]",$this->get('veldnaam')))
	    $this->setError('veldnaam',vt("Het veld bevatte ongeldige tekens, deze zijn verwijderd."));

	  $this->set('veldnaam',ereg_replace("[^A-Za-z0-9_]", "", $this->get('veldnaam')));

	  $query="SHOW COLUMNS FROM CRM_naw LIKE '".$this->get('veldnaam')."'";
	  if($db->QRecords($query) && $this->get('id') < 1)
	    $this->setError('veldnaam',vt("Het opgegeven veld zit al in de database. (CRM_naw tabel)"));
      
    if($this->error==false)
    {
      $extraTabellen=array('Portefeuilles','CRM_naw_kontaktpersoon','CRM_naw_adressen','laatstePortefeuilleWaarde');
      foreach($extraTabellen as $tabel)
      {
	      $query="SHOW COLUMNS FROM $tabel LIKE '".$this->get('veldnaam')."'";
	      if($db->QRecords($query) && $this->get('id') < 1)
	        $this->setError('veldnaam',vtb("Het opgegeven veld zit al in de database (%s tabel).", array($tabel)));
      }
    }
    
    
 	  if($this->get('veldtype')=='Tekst' &&($this->get('aantalTekens')<1 || $this->get('aantalTekens')>255))
    {
      $this->setError('aantalTekens', vt("Het aantal tekens moet tussen 1 en 255 tekens liggen.").$this->get('aantalTekens'));
    }
	  if($this->get('omschrijving')=="")
	    $this->setError('omschrijving',vt("Mag niet leeg zijn."));

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  if($type=='delete')
	    return false;

		if($_SESSION['usersession']['superuser'])
		  return true;
		else
		{
		  switch ($type)
		  {
		    case "edit":
          return GetCRMAccess(2);
          break;
        case "delete":
          return false;
          break;
        default:
          return false;
          break;
      }
		}
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "CRM_eigenVelden";
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

		$this->addField('veldnaam',
													array("description"=>"veldnaam",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('omschrijving',
													array("description"=>"omschrijving NL",
													"default_value"=>"",
													"db_size"=>"150",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"80",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $this->addField('omschrijving_en',
      array("description"=>"omschrijving EN",
        "default_value"=>"",
        "db_size"=>"150",
        "db_type"=>"varchar",
        "form_type"=>"text",
        "form_size"=>"80",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('omschrijving_fr',
      array("description"=>"omschrijving FR",
        "default_value"=>"",
        "db_size"=>"150",
        "db_type"=>"varchar",
        "form_type"=>"text",
        "form_size"=>"80",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));

    $this->addField('omschrijving_du',
      array("description"=>"omschrijving DU",
        "default_value"=>"",
        "db_size"=>"150",
        "db_type"=>"varchar",
        "form_type"=>"text",
        "form_size"=>"80",
        "form_visible"=>true,
        "list_visible"=>true,
        "list_width"=>"100",
        "list_align"=>"left",
        "list_search"=>false,
        "list_order"=>"true"));


    $this->addField('aantalTekens',
                    array("description"=>"aantalTekens",
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

		$this->addField('veldtype',
													array("description"=>"veldtype",
													"default_value"=>"Tekst",
													"db_size"=>"60",
													"db_type"=>"varchar",
													"form_options"=>array('Tekst','Memo','Getal','Datum','Trekveld','Checkbox'),
													"form_type"=>"select",
													"form_size"=>"60",
													"form_visible"=>true,
													"form_extra"=>"onchange=\"if(this.value =='Tekst'){ $('#divAantalTekens').show();}else { $('#divAantalTekens').hide();}\"",
													"form_select_option_notempty"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
//global $__CRMvars;
//listarray($__CRMvars["selectieTypen"]);exit;

		$this->addField('trekveldSelectieveld',
													array("description"=>"Trekveld selectievelden",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"select_query"=>"SELECT Module,Module FROM CRM_selectievelden GROUP BY  Module ORDER BY Module ",
													"form_size"=>"40",
													"form_visible"=>true,
													"form_select_option_notempty"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                           
		$this->addField('relatieSoort',
													array("description"=>"Gebruiken als relatiesoort",
													"db_size"=>"1",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"categorie"=>"Gegevens"));
                          
 		$this->addField('extraVeldData',
													array("description"=>"extraVeldData",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"text",
													"form_type"=>"text",
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



  }
}
?>