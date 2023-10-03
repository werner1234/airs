<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 augustus 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/02/19 14:48:34 $
    File Versie         : $Revision: 1.17 $
 		
    $Log: externeQueries.php,v $
    Revision 1.17  2020/02/19 14:48:34  rvv
    *** empty log message ***

    Revision 1.16  2018/11/24 12:39:07  rvv
    *** empty log message ***

    Revision 1.15  2018/04/28 18:32:32  rvv
    *** empty log message ***

    Revision 1.14  2017/09/02 07:35:13  rvv
    *** empty log message ***

    Revision 1.13  2017/08/30 14:56:08  rvv
    *** empty log message ***

    Revision 1.12  2017/02/13 06:40:22  rvv
    *** empty log message ***

    Revision 1.11  2017/02/08 16:18:22  rvv
    *** empty log message ***

    Revision 1.10  2016/10/23 11:36:50  rvv
    *** empty log message ***

    Revision 1.9  2016/01/06 16:34:47  rvv
    *** empty log message ***

    Revision 1.8  2015/12/20 16:50:27  rvv
    *** empty log message ***

    Revision 1.7  2015/09/23 14:57:02  rvv
    *** empty log message ***

    Revision 1.6  2014/10/12 09:03:14  rvv
    *** empty log message ***

    Revision 1.5  2014/09/17 15:18:38  rvv
    *** empty log message ***

    Revision 1.4  2014/08/27 15:52:52  rvv
    *** empty log message ***

    Revision 1.3  2014/08/20 15:27:41  rvv
    *** empty log message ***

    Revision 1.2  2014/08/09 14:44:18  rvv
    *** empty log message ***

    Revision 1.1  2014/08/02 15:21:07  rvv
    *** empty log message ***

 		
 	
*/

class ExterneQueries extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function ExterneQueries()
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
	  $filterWaarden=array('ALTER','BACKUP','CACHE INDEX','CHANGE','COMMIT','CREATE','DELETE','DROP','FLUSH','GRANT','HANDLER','INSERT','KILL','LOAD','LOCK','PURGE','RENAME','REPLACE','RESET','REVOKE','ROLLBACK','SAVEPOINT','SET','SHOW','START','STOP','TRUNCATE','UNLOCK','UPDATE');
    $query=strtoupper($this->get('query'));
    foreach($filterWaarden as $filter)
    {
      if(strpos($query,$filter.' ')!==false)
        $this->setError("query", vtb("Query bevat %s", array($filter)));
    }
    if(strpos($query,"SELECT")!==false)
    {
      $query="explain ".$this->get('query');
      $db=new DB();
      if($db->QRecords($query)==0)
        $this->setError("query",vt("explain op query mislukt."));
    }
    
    //
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
    global $USR,$__appvar;
       
    if($USR=='JBR' || $USR=='FEGT'|| $USR=='AIRS'|| $USR=='Airs' || $USR=='MHO') //$__appvar['bedrijf']=='HOME' &&
        return true;

    return false;
	}
	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "";
    $this->data['table']  = "externeQueries";
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

		$this->addField('titel',
													array("description"=>"titel",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('frequentie',
													array("description"=>"Frequentie",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_options"=>array(0=>'',1=>'Dagelijks',2=>'Wekelijks',3=>'Maandelijks',4=>'Kwartaal',5=>'Jaarultimo',6=>'Ad-hoc',7=>'Halfjaarlijks'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $this->addField('uitvoer',
                    array("description"=>"Email uitvoer",
                          "default_value"=>"",
                          "db_size"=>"3",
                          "db_type"=>"tinyint",
                          "form_type"=>"selectKeyed",
                          "form_select_option_notempty" => true,
                          "form_options"=>array(0=>'excel',1=>'csv'),
                          "form_size"=>"3",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
    $this->addField('categorie',
													array("description"=>"Categorie",
													"default_value"=>"",
													"db_size"=>"30",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
                          "select_query"=>"SELECT categorie,omschrijving FROM externeQueryCategorien ORDER BY volgorde",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('omschrijving',
													array("description"=>"query omschrijving",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"100",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"400",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('query',
													array("description"=>"query",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"mediumtext",
													"form_type"=>"textarea",
													"form_size"=>"100",
                          "form_rows"=>"15",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memo',
													array("description"=>"Memo",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"100",
                          "form_rows"=>5,
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('homeOnly',
													array("description"=>"home",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('controlekolommen',
													array("description"=>"Controlekolommen tonen",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                                                    
		$this->addField('run_date',
													array("description"=>"run_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('autoEmailadres',
										array("description"=>"Emailadres",
													"default_value"=>"",
													"db_size"=>"100",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('autoVanaf',
										array("description"=>"Automatisch vanaf",
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

		$this->addField('autoVanafUur',
										array("description"=>"Automatisch vanaf uur",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
													"form_options"=>array(0=>'00',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',16=>'16',17=>'17',18=>'18',19=>'19',20=>'20',21=>'21',22=>'22',23=>'23'),
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('run_user',
													array("description"=>"run_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
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