<?php
/*
    AE-ICT CODEX source module versie 1.6, 6 mei 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/07/25 15:39:24 $
    File Versie         : $Revision: 1.15 $

    $Log: IndexPerBeleggingscategorie.php,v $
    Revision 1.15  2018/07/25 15:39:24  rvv
    *** empty log message ***

    Revision 1.14  2017/07/15 16:07:55  rvv
    *** empty log message ***

    Revision 1.13  2016/12/18 13:18:43  rvv
    *** empty log message ***

    Revision 1.12  2016/08/27 16:22:30  rvv
    *** empty log message ***

    Revision 1.11  2016/04/17 16:50:18  rvv
    *** empty log message ***

    Revision 1.10  2016/04/16 17:09:56  rvv
    *** empty log message ***

    Revision 1.9  2016/04/13 16:25:46  rvv
    *** empty log message ***

    Revision 1.8  2015/08/23 11:34:05  rvv
    *** empty log message ***

    Revision 1.7  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.6  2014/11/30 13:04:47  rvv
    *** empty log message ***

    Revision 1.5  2014/03/16 11:15:48  rvv
    *** empty log message ***

    Revision 1.4  2014/03/08 16:59:39  rvv
    *** empty log message ***

    Revision 1.3  2012/11/10 15:42:49  rvv
    *** empty log message ***

    Revision 1.2  2012/07/22 12:53:11  rvv
    *** empty log message ***

    Revision 1.1  2008/05/06 10:16:46  rvv
    *** empty log message ***



*/

class IndexPerBeleggingscategorie extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function IndexPerBeleggingscategorie()
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
	  ($this->get("Vermogensbeheerder")=="")?$this->setError("Vermogensbeheerder",vt("Mag niet leeg zijn!")):true;
	  
    if($this->get("Categoriesoort")!="")
    {
      ($this->get("Categorie")=="")?$this->setError("Categorie",vt("Mag niet leeg zijn!")):true;
    }
    else
    {
      ($this->get("Beleggingscategorie")=="")?$this->setError("Beleggingscategorie",vt("Mag niet leeg zijn!")):true;
    }
    
    if($this->get("Categorie")!="" && $this->get("Beleggingscategorie")!="")
    {
      $this->setError("Beleggingscategorie",vt("Koppel een beleggingscategorie of een categoriesoort. (Niet beiden.)"));
      $this->setError("Categoriesoort",vt("Koppel een beleggingscategorie of een categoriesoort. (Niet beiden.)"));
    }
      
	  ($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;

	  if($this->get("vanaf") <> '')
	  {
	    $datumJul=db2jul($this->get("vanaf"));
	    $datum=mktime(0,0,0,date('m',$datumJul)+1,0,date('Y',$datumJul));
	    $datum=date("Y-m-d",$datum);
	    if($this->get("vanaf") <> $datum)
	      $this->setError("vanaf",vt("Moet de laatste dag van een mand zijn."));
	  }

	 // ($this->get("Vanaf")=='')?$this->setError("Fonds","Mag niet leeg zijn!"):true;

	  $DB = new DB();
	  $query = "SELECT id FROM IndexPerBeleggingscategorie
	            WHERE
	            Vermogensbeheerder   = '".$this->get("Vermogensbeheerder")."' AND
	            Beleggingscategorie  = '".$this->get("Beleggingscategorie")."' AND
	            Fonds                = '".$this->get("Fonds")."' AND
              Portefeuille         = '".$this->get("Portefeuille")."'";
	  $DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Vermogensbeheerder", vtb("%s bestaat al"));
			$this->setError("Beleggingscategorie",$this->get("Beleggingscategorie")." bestaat al");
			$this->setError("Fonds",$this->get("Fonds")." bestaat al");
      $this->setError("Portefeuille",$this->get("Portefeuille")." bestaat al");
		}

		$DB->lookupRecordByQuery("SELECT Fonds FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->get("Fonds"))."' ");
		if($DB->records() <= 0) {
			$this->setError("Fonds",$this->get("Fonds")." is een onbekend fonds");
		}

    // Portefeuille mag leeg zijn maar wanner gevuld moet deze wel aanwezig zijn.
    $portefeuille = $this->get("Portefeuille");
    if ( ! empty($portefeuille) ) {
      $DB->lookupRecordByQuery("SELECT Portefeuille FROM Portefeuilles WHERE Portefeuille = '".mysql_real_escape_string($portefeuille)."' ");
      if($DB->records() <= 0) {
        $this->setError("Portefeuille",$portefeuille." is een onbekende portefeuille");
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
     return checkAccess($type);
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "IndexPerBeleggingscategorie";
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

		$this->addField('Vermogensbeheerder',
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

		$this->addField('Beleggingscategorie',
													array("description"=>"Beleggingscategorie",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Beleggingscategorie,Beleggingscategorie FROM Beleggingscategorien ORDER BY Beleggingscategorie ",
													"form_type"=>"selectKeyed",
													"form_size"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Beleggingscategorien"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
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

		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													'autocomplete' => array(
														'table'        => 'Portefeuilles',
														'prefix'       => true,
														'returnType'   => 'expanded',
														'extra_fields' => array(
															'Portefeuille',
															'Client',
															'id',
														),
														'label'        => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
														'searchable'   => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
														'field_value'  => array('Portefeuilles.Portefeuille'),
														'value'        => 'Portefeuilles.Portefeuille',
													),
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));

		$this->addField('Categoriesoort',
													array("description"=>"Categoriesoort",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_options"=>array('Beleggingscategorien'=>'Beleggingscategorien','Beleggingssectoren'=>'Beleggingssectoren',
                                                'Regios'=>'Regios','AttributieCategorien'=>'AttributieCategorien','afmCategorien'=>'afmCategorien',
                                                'SoortOvereenkomsten'=>'SoortOvereenkomsten','Zorgplicht'=>'Zorgplichtcategorien','DuurzaamCategorie'=>'DuurzaamCategorien' ),
													"form_size"=>"50",
													"form_visible"=>true,
													"form_extra"=>"onchange='javascript:selectieChanged();'",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Categorie',
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
                          'keyCondition'=>'Categoriesoort',
                          'keyIn'=>'Beleggingscategorien,Beleggingssectoren,Regios,AttributieCategorien,afmCategorien,SoortOvereenkomsten,Zorgplichtcategorien,DuurzaamCategorien'));

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