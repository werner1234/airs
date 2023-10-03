<?php
/*
    AE-ICT CODEX source module versie 1.6, 4 december 2010
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/05/08 15:01:04 $
    File Versie         : $Revision: 1.4 $

    $Log: benchmarkverdelingVanaf.php,v $
    Revision 1.4  2020/05/08 15:01:04  rm
    8593 Benchmarkverdeling: gebruik AJAX-lookup

    Revision 1.3  2020/05/06 14:55:04  rvv
    *** empty log message ***

    Revision 1.2  2017/09/13 09:58:46  rvv
    *** empty log message ***

    Revision 1.1  2017/08/05 17:22:37  rvv
    *** empty log message ***

    Revision 1.3  2014/11/30 13:04:47  rvv
    *** empty log message ***

    Revision 1.2  2014/11/01 22:08:02  rvv
    *** empty log message ***

    Revision 1.1  2010/12/05 09:46:41  rvv
    *** empty log message ***



*/

class BenchmarkverdelingVanaf extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function BenchmarkverdelingVanaf()
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
		($this->get("benchmark")=="")?$this->setError("benchmark",vt("Mag niet leeg zijn!")):true;
		($this->get("fonds")=="")?$this->setError("fonds",vt("Mag niet leeg zijn!")):true;
		($this->get("percentage")=="")?$this->setError("percentage",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM benchmarkverdelingVanaf WHERE ".
							" benchmark = '".$this->get("benchmark")."' AND ".
			        " vanaf = '".$this->get("vanaf")."' AND ".
							" fonds = '".$this->get("fonds")."'";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("benchmark",vt("deze combinatie bestaat al"));
			$this->setError("fonds",vt("deze combinatie bestaat al"));
			$this->setError("vanaf",vt("deze combinatie bestaat al"));
		}
    
    $query  = "SELECT id FROM benchmarkverdeling WHERE benchmark = '".$this->get("benchmark")."' ";
    $DB->SQL($query);
    $DB->Query();
    if($DB->records() >0)
    {
      $this->setError("benchmark",vtb("%s is al aanwezig in de benchmarkverdeling tabel.", array($this->get("benchmark"))));
    }
    
    //validatie of fonds bestaat
    $fondsObj = new Fonds();
    if ( $this->get("benchmark") !== "" ) {
      $benchmarkExists = $fondsObj->parseBySearch(array('fonds' => $this->get('benchmark')), 'Fonds');
      ( empty($benchmarkExists) ) ? $this->setError("benchmark",vt("Fonds niet gevonden")):true;
    }
    
    if ( $this->get("fonds") !== "" ) {
      $fondsExists = $fondsObj->parseBySearch(array('fonds' => $this->get('fonds')), 'Fonds');
      ( empty($fondsExists) ) ? $this->setError("fonds",vt("Fonds niet gevonden")):true;
    }
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    return checkAccess();
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "benchmarkverdelingVanaf";
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

		$this->addField('benchmark',
													array("description"=>"benchmark",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
//													"form_type"=>"selectKeyed",
//													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Omschrijving",
                          'form_type' =>'text',
                          'autocomplete' => array(
                            'table'        => 'Fondsen',
                            'label'        => array(
                              'Fondsen.Fonds',
                              'Fondsen.ISINCode',
                              'combine' => '({Valuta})'
                            ),
                            'extra_fields' => array('*'),
                            'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving', 'Fondsen.FondsImportCode'),
                            'field_value' => array(
                              'Fonds',
                            ),
                            'value' => 'Fonds',
                            'conditions'   => array(
                              'AND' => array(
                                '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00")',
                              )
                            )
                          ),
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
//													"form_type"=>"selectKeyed",
//													'select_query' => "SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = '0000-00-00' ORDER BY Omschrijving",
                          'form_type' =>'text',
                          'autocomplete' => array(
                            'table'        => 'Fondsen',
                            'label'        => array(
                              'Fondsen.Fonds',
                              'Fondsen.ISINCode',
                              'combine' => '({Valuta})'
                            ),
                            'extra_fields' => array('*'),
                            'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving', 'Fondsen.FondsImportCode'),
                            'field_value' => array(
                              'Fonds',
                            ),
                            'value' => 'Fonds',
                            'conditions'   => array(
                              'AND' => array(
                                '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00")',
                              )
                            )
                          ),
													"form_size"=>"25",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Fondsen"));

		$this->addField('percentage',
													array("description"=>"percentage",
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

		$this->addField('vanaf',
										array("description"=>"Geldig vanaf",
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

		$this->addField('toelichting',
													array("description"=>"Toelichting",
								    			"db_size"=>"200",
													"db_type"=>"varchar",
                          "form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"
													));
                          
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