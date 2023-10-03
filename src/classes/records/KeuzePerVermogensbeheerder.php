<?php
/*
    AE-ICT CODEX source module versie 1.6, 1 december 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/02/22 18:43:54 $
    File Versie         : $Revision: 1.19 $

    $Log: KeuzePerVermogensbeheerder.php,v $
    Revision 1.19  2020/02/22 18:43:54  rvv
    *** empty log message ***

    Revision 1.18  2018/09/15 17:37:10  rvv
    *** empty log message ***

    Revision 1.17  2018/07/21 15:51:44  rvv
    *** empty log message ***

    Revision 1.16  2017/12/20 16:57:25  rvv
    *** empty log message ***

    Revision 1.15  2017/12/02 19:09:44  rvv
    *** empty log message ***

    Revision 1.14  2016/12/21 16:29:00  rvv
    *** empty log message ***

    Revision 1.13  2016/07/16 16:52:53  rvv
    *** empty log message ***

    Revision 1.12  2016/06/19 15:18:50  rvv
    *** empty log message ***

    Revision 1.11  2015/12/13 08:59:27  rvv
    *** empty log message ***

    Revision 1.10  2015/11/29 13:05:52  rvv
    *** empty log message ***

    Revision 1.9  2015/11/25 17:04:18  rvv
    *** empty log message ***

    Revision 1.8  2015/01/03 16:07:20  rvv
    *** empty log message ***

    Revision 1.7  2012/10/31 16:55:25  rvv
    *** empty log message ***

    Revision 1.6  2012/03/11 17:15:23  rvv
    *** empty log message ***

    Revision 1.5  2012/03/04 11:17:49  rvv
    *** empty log message ***

    Revision 1.4  2011/12/18 14:22:17  rvv
    *** empty log message ***

    Revision 1.3  2011/08/31 15:18:50  rvv
    *** empty log message ***

    Revision 1.2  2011/06/25 20:08:36  rvv
    *** empty log message ***

    Revision 1.1  2010/12/01 18:05:58  rvv
    *** empty log message ***



*/

class KeuzePerVermogensbeheerder extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function KeuzePerVermogensbeheerder()
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
		($this->get("categorie")=="")?$this->setError("categorie",vt("Mag niet leeg zijn!")):true;
		($this->get("waarde")=="")?$this->setError("waarde",vt("Mag niet leeg zijn!")):true;

		$query  = "SELECT id FROM KeuzePerVermogensbeheerder WHERE ".
							" categorie = '".$this->get("categorie")."' AND ".
							" waarde = '".$this->get("waarde")."' AND ".
							" vermogensbeheerder = '".$this->get("vermogensbeheerder")."'";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();
		if($DB->records() >0 && $this->get("id") <> $data['id'])
		{
			$this->setError("vermogensbeheerder",vt("deze combinatie bestaat al"));
			$this->setError("categorie",vt("deze combinatie bestaat al"));
			$this->setError("waarde",vt("deze combinatie bestaat al"));
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
    $this->data['table']  = "KeuzePerVermogensbeheerder";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('vermogensbeheerder',
													array("description"=>"vermogensbeheerder",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Vermogensbeheerder,Vermogensbeheerder, concat(Vermogensbeheerder,' - ',naam) FROM Vermogensbeheerders ORDER BY Vermogensbeheerder",
													"form_type"=>"selectKeyed",
													'form_select_option_notempty'=>true,
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Vermogensbeheerders"));

		$this->addField('categorie',
													array("description"=>"categorie",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_options"=>array('Beleggingscategorien'=>'Beleggingscategorien','Beleggingssectoren'=>'Beleggingssectoren',
                                                'Regios'=>'Regios','AttributieCategorien'=>'AttributieCategorien','afmCategorien'=>'afmCategorien',
																								'SoortOvereenkomsten'=>'SoortOvereenkomsten', 'Zorgplichtcategorien'=>'Zorgplichtcategorien',
														                    'Orderredenen'=>'Orderredenen','Grootboekrekeningen'=>'Grootboekrekeningen','DuurzaamCategorien'=>'DuurzaamCategorien',
                                                'toelichtingStortOnttr'=>'toelichtingStortOnttr'),
													"form_size"=>"50",
													"form_visible"=>true,
													"form_extra"=>"onchange='javascript:selectieChanged();'",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('waarde',
													array("description"=>"waarde",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'form_select_option_notempty'=>true,
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          'keyCondition'=>'categorie',
                          'keyIn'=>'Beleggingscategorien,Beleggingssectoren,Regios,AttributieCategorien,afmCategorien,SoortOvereenkomsten,DuurzaamCategorien,Orderredenen,toelichtingStortOnttr'));

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

		$this->addField('categorieIXP',
										array("description"=>"IXP categorie",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_options"=>array(),
													"form_size"=>"50",
													"form_visible"=>true,
													"form_extra"=>"DISABLED",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('AfmKostensoort',
										array("description"=>"AFM Kostensoort",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"select_query"=>"SELECT AfmKostensoort,AfmKostensoort FROM AFMKostensoorten ORDER BY AfmKostensoort",
													"form_type"=>"selectKeyed",
													"form_size"=>"50",
													"form_visible"=>true,
													"form_extra"=>"DISABLED",
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													'keyIn'=>'AFMKostensoorten'));

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