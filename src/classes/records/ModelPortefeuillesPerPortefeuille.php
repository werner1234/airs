<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 oktober 2015
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/08/28 11:56:13 $
    File Versie         : $Revision: 1.12 $
 		
    $Log: ModelPortefeuillesPerPortefeuille.php,v $
    Revision 1.12  2019/08/28 11:56:13  rm
    8010

    Revision 1.11  2018/07/18 15:51:43  rvv
    *** empty log message ***

    Revision 1.10  2018/07/14 13:58:15  rvv
    *** empty log message ***

    Revision 1.9  2018/06/20 16:35:08  rvv
    *** empty log message ***

    Revision 1.8  2018/06/20 16:33:44  rvv
    *** empty log message ***

    Revision 1.7  2017/10/04 14:53:11  rm
    no message

    Revision 1.6  2016/11/19 18:58:47  rvv
    *** empty log message ***

    Revision 1.5  2016/11/13 16:23:51  rvv
    *** empty log message ***

    Revision 1.4  2016/10/19 06:58:39  cvs
    call 3856

    Revision 1.3  2016/10/02 12:30:09  rvv
    *** empty log message ***

    Revision 1.2  2016/09/04 14:37:40  rvv
    *** empty log message ***

    Revision 1.1  2015/10/07 19:41:38  rvv
    *** empty log message ***

 		
 	
*/

class ModelPortefeuillesPerPortefeuille extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ModelPortefeuillesPerPortefeuille()
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
		$checkPortefeuille = $this->get("Portefeuille");
		if ( ! empty($checkPortefeuille) ) {
			$portefeuilleObj = new Portefeuilles();
			$portefeuilleData = $portefeuilleObj->parseBySearch(array('Portefeuille' => $checkPortefeuille));

			if ( empty ($portefeuilleData) )
			{
				$portefeuilleObj = new GeconsolideerdePortefeuilles();
				$portefeuilleData = $portefeuilleObj->parseBySearch(array('VirtuelePortefeuille' => $checkPortefeuille));
				if ( empty ($portefeuilleData) )
					$this->setError("Portefeuille",vt("Portefeuille onbekend!"));
			}
		}


    if ( isset ($_GET['ModelPortefeuille']) && ! empty ($_GET['ModelPortefeuille']) ) {
      $db = new DB();
      $query = "SELECT * FROM `ModelPortefeuilles` WHERE `Portefeuille` = '" . mysql_real_escape_string($_GET['ModelPortefeuille']) . "';";
      if ( $db->QRecords($query) == 0 ) {
        $this->setError("ModelPortefeuille",vt("ModelPortefeuille is onbekend!"));
      }
    } else {
      $this->setError("ModelPortefeuille",vt("ModelPortefeuille mag niet leeg zijn!"));
    }
    
    ($this->get("Portefeuille")=="")?$this->setError("Portefeuille",vt("Mag niet leeg zijn!")):true;
    ($this->get("ModelPortefeuille")=="")?$this->setError("ModelPortefeuille",vt("Mag niet leeg zijn!")):true;
    ($this->get("Vanaf")=="")?$this->setError("Vanaf",vt("Mag niet leeg zijn!")):true;

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
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
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "ModelPortefeuillesPerPortefeuille";
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

		$this->addField('Portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
//													"select_query"=>"SELECT Portefeuille,Portefeuille FROM Portefeuilles ORDER BY Portefeuille",
//													"select_query_ajax"=>"SELECT Portefeuille, Portefeuille FROM Portefeuilles WHERE Portefeuille='{value}'",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,
      										"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles,GeconsolideerdePortefeuilles"));


		$this->addField('ModelPortefeuille',
													array("description"=>"ModelPortefeuille",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"select_query"=>"SELECT ModelPortefeuilles.Portefeuille,if(ModelPortefeuilles.fixed=1,concat(ModelPortefeuilles.Portefeuille,' (FX)'),concat(ModelPortefeuilles.Portefeuille,' (Dyn)')) AS displayline FROM ModelPortefeuilles 
        JOIN Portefeuilles ON ModelPortefeuilles.Portefeuille=Portefeuilles.Portefeuille 
        WHERE Portefeuilles.Einddatum>now() AND ModelPortefeuilles.Fixed<2 ORDER BY Portefeuille",
													"select_query_ajax"=>"SELECT Portefeuille, if(ModelPortefeuilles.fixed=1,concat(Portefeuille,' (FX)'),concat(Portefeuille,' (Dyn)')) FROM ModelPortefeuilles WHERE Portefeuille='{value}'",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Portefeuilles"));
                          
		$this->addField('Percentage',
													array("description"=>"Percentage",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Vanaf',
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