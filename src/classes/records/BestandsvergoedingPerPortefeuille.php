<?php
/*
    AE-ICT CODEX source module versie 1.6, 18 mei 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/12/03 17:09:47 $
    File Versie         : $Revision: 1.9 $

    $Log: BestandsvergoedingPerPortefeuille.php,v $
    Revision 1.9  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.8  2012/10/24 15:41:59  rvv
    *** empty log message ***

    Revision 1.7  2012/10/02 16:15:29  rvv
    *** empty log message ***

    Revision 1.6  2012/09/30 11:12:21  rvv
    *** empty log message ***

    Revision 1.5  2011/12/11 10:55:09  rvv
    *** empty log message ***

    Revision 1.4  2011/12/07 19:12:12  rvv
    *** empty log message ***

    Revision 1.3  2011/11/19 15:36:03  rvv
    *** empty log message ***

    Revision 1.2  2011/09/28 18:40:59  rvv
    *** empty log message ***

    Revision 1.1  2011/05/18 16:49:12  rvv
    *** empty log message ***



*/

class BestandsvergoedingPerPortefeuille extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function BestandsvergoedingPerPortefeuille()
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

	  ($this->get("portefeuille")=="")?$this->setError("portefeuille",vt("Mag niet leeg zijn!")):true;
	  ($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;
	  ($this->get("bedragBerekend")=="")?$this->setError("bedragBerekend",vt("Mag niet leeg zijn!")):true;
		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  if($this->get('datumUitbetaald') > 0)
      return false;
	
    $check=checkAccess($type);
    if($check == true)
      return $check;
   return $_SESSION['usersession']['gebruiker']['bestandsvergoedingEdit'];
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "BestandsvergoedingPerPortefeuille";
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

		$this->addField('bestandsvergoedingId',
													array("description"=>"bestandsvergoedingId",
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



$this->addField('portefeuille',
													array("description"=>"portefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Portefeuille, concat(Portefeuille,' - ',Client) FROM Portefeuilles WHERE BestandsvergoedingUitkeren=1 AND Einddatum > NOW() ORDER BY Portefeuille",
													"select_query_ajax"=>"SELECT Portefeuille, concat(Portefeuille,' - ',Client) FROM Portefeuilles WHERE Portefeuille='{value}' AND BestandsvergoedingUitkeren=1 AND Einddatum > NOW()",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));

			$this->addField('Fonds',
													array("description"=>"fonds",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds, concat(Fonds,' - ',ISINCode) FROM Fondsen WHERE Fondseenheid=1 AND (EindDatum > NOW() OR EindDatum = '0000-00-00')  ORDER BY Fonds",

													"form_visible"=>true,"list_width"=>"150",
													'form_class'=>'" style="width:350px',
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));
//'select_query_ajax' => "SELECT Fonds,concat(Fonds,' - ',ISINCode) FROM Fondsen WHERE Fonds='{value}'",


		$this->addField('bedragBerekend',
													array("description"=>"bedragBerekend",
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

		$this->addField('bedragUitbetaald',
													array("description"=>"bedragUitbetaald",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('datumUitbetaald',
													array("description"=>"datumUitbetaald",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
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