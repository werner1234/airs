<?php
/*
    AE-ICT CODEX source module versie 1.6, 4 augustus 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/05/08 14:44:33 $
    File Versie         : $Revision: 1.3 $

    $Log: custom_templates.php,v $
    Revision 1.3  2020/05/08 14:44:33  rm
    8541 emailings eMail opmaak: via nieuwe templates

    Revision 1.2  2020/04/15 14:14:24  rm
    8144 Losse factuur: templates gebruiken

    Revision 1.1  2020/04/07 13:54:46  rm
    8144 Losse factuur: templates gebruiken

    Revision 1.3  2016/03/20 14:38:48  rvv
    *** empty log message ***

    Revision 1.2  2014/02/22 18:38:01  rvv
    *** empty log message ***

    Revision 1.1  2012/08/05 10:42:12  rvv
    *** empty log message ***



*/

class custom_templates extends Table
{
  /*
  * Object vars
  */

  var $data = array();
  //crmSjabloon
  var $template = array(
    'factuurLos' => array (
      'name'  => 'Factuur Los',
      'dbFieldSelectie' => false,
      'fields'    => array (
        'factuuronderwerp' => array (
          'description' => 'Onderwerp',
          'type'  => 'text'
        ),
        'tekstblok1' => array (
          'description' => 'Tekstblok 1',
          'type'  => 'textarea'
        ),
        'tekstblok2' => array (
          'description' => 'Tekstblok 2',
          'type'  => 'textarea'
        ),
        'ond1' => array (
          'description' => 'Onderwerp 1',
          'type'  => 'text'
        ),
        'ond2' => array (
          'description' => 'Onderwerp 2',
          'type'  => 'text'
        ),
        'ond3' => array (
          'description' => 'Onderwerp 3',
          'type'  => 'text'
        )
      )
    ),
    'crmSjabloon' => array (
      'name'  => 'CRM Sjabloon',
      'dbFieldSelectie' => true,
      'fields'    => array (
        'tekstblok' => array (
          'description'           => 'Tekstblok',
          'type'                  => 'textEditor'
        ),
        
        'headerp1' => array (
          'description'           => 'Header p1',
          'type'                  => 'textEditor',
          'visibleInTemplate'     => false
        ),
        'footerp1' => array (
          'description'           => 'Footer p1',
          'type'                  => 'textEditor',
          'visibleInTemplate'     => false
        ),
        'headerp2' => array (
          'description'           => 'Header p2',
          'type'                  => 'textEditor',
          'visibleInTemplate'     => false
        ),
        'footerp2' => array (
          'description'           => 'Footer p2',
          'type'                  => 'textEditor',
          'visibleInTemplate'     => false
        ),
        
      )
    ),
    'crmEmailLos' => array (
      'name'  => 'Crm e-mail',
      'dbFieldSelectie' => true,
      'fields'    => array (

        'CC' => array (
          'description' => 'eMail cc emailadres',
          'type'  => 'text'
        ),
        'BCC' => array (
          'description' => 'eMail bcc emailadres',
          'type'  => 'text'
        ),
        'onderwerp' => array (
          'description' => 'eMail onderwerp',
          'type'  => 'text'
        ),
        'body' => array (
          'description' => 'Body tekst',
          'type'  => 'textEditor'
        ),
      )
    ),
    'backofficeEmail' => array (
      'name'  => 'Backoffice e-mail',
      'dbFieldSelectie' => true,
      'fields'    => array (
        'afzender' => array (
          'description' => 'E-Mail afzender',
          'type'  => 'text'
        ),
        'afzenderEmail' => array (
          'description' => 'E-Mail afzender emailadres',
          'type'  => 'text'
        ),
        'ccEmail' => array (
          'description' => 'eMail cc emailadres',
          'type'  => 'text'
        ),
        'bccEmail' => array (
          'description' => 'eMail bcc emailadres',
          'type'  => 'text'
        ),
        'onderwerp' => array (
          'description' => 'eMail onderwerp',
          'type'  => 'text'
        ),
        'email' => array (
          'description' => 'Body tekst',
          'type'  => 'textEditor'
        ),
      )
    ),
    
    'crmEmailings' => array (
      'name'  => 'CRM E-mailings',
      'dbFieldSelectie' => true,
      'fields'    => array (
        'body' => array (
          'description' => 'Body tekst',
          'type'  => 'textEditor'
        ),
        'onderwerp' => array (
          'description' => 'Onderwerp',
          'type'  => 'text'
        ),
        'ccEmail' => array (
          'description' => 'eMail cc emailadres',
          'type'  => 'text',
          'translatable' => false,
        ),
        'bccEmail' => array (
          'description' => 'eMail bcc emailadres',
          'type'  => 'text',
          'translatable' => false,
        ),
      )
    )
  );

  /*$_SESSION['usersession']['gebruiker']['emailHandtekening']
  * Constructor
  */
  function custom_templates($customTeplate = null)
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);

    foreach ( $this->template as $templateKey => $templateData ) {
      $curTemplate[$templateKey] = $templateData['name'];
    }
    $this->customTeplate = $customTeplate;
    $this->data['fields']['categorie']['form_options'] = $curTemplate;
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
    
    ($this->get("naam")=="")?$this->setError("naam",vt("Mag niet leeg zijn!")):true;
    ($this->get("categorie")=="")?$this->setError("categorie",vt("Mag niet leeg zijn!")):true;
    $valid = ($this->error==false)?true:false;
    
    if ( $valid === true ) {
      $templateExist = $this->customTeplate->templateExists($this->get('categorie'), $this->get('naam'), true);
      
      // Nieuwe template, naam en categorie combinatie bestaat al = fout
      if ( $templateExist !== false && $this->get('id') == 0 ) {
        $valid = false;
        $this->setError("naam"," ");
        $this->setError("categorie",vt("Combinatie naam en categorie bestaat al!"));
      }
      // Bestaande template, combinatie van naam en categorie bestaat al in een andere template = fout
      elseif ( $templateExist !== false && $this->get('id') > 0 && $templateExist['id'] != $this->get('id') ) {
        $valid = false;
        $this->setError("naam"," ");
        $this->setError("categorie",vt("Combinatie naam en categorie bestaat al!"));
      }
    }
		
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return true;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "custom_templates";
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

		$this->addField('naam',
													array("description"=>"Naam",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('template',
													array("description"=>"Template",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"mediumblob",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('categorie',
													array("description"=>"categorie",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "form_options" => array('factuurLos' => 'Factuur los')
                          ));
                          
 		$this->addField('verplichteVelden',
													array("description"=>"Verplichte velden",
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
  
    $this->addField('meertalig',
                    array("description"=>"Meertalig",
                          "default_value"=>"",
                          "db_size"=>"0",
                          "db_type"=>"text",
                          "form_type"=>"checkbox",
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