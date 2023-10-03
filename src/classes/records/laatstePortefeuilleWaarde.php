<?php
/*
    AE-ICT CODEX source module versie 1.6, 13 juni 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/02/10 18:04:12 $
    File Versie         : $Revision: 1.19 $

    $Log: laatstePortefeuilleWaarde.php,v $
    Revision 1.19  2018/02/10 18:04:12  rvv
    *** empty log message ***

    Revision 1.18  2017/12/18 07:22:26  rvv
    *** empty log message ***

    Revision 1.17  2017/01/21 17:05:18  rvv
    *** empty log message ***

    Revision 1.16  2017/01/07 16:08:15  rvv
    *** empty log message ***

    Revision 1.15  2016/12/28 19:35:52  rvv
    *** empty log message ***

    Revision 1.14  2015/11/29 13:05:52  rvv
    *** empty log message ***

    Revision 1.13  2015/04/04 15:12:31  rvv
    *** empty log message ***

    Revision 1.12  2014/12/03 17:09:47  rvv
    *** empty log message ***

    Revision 1.11  2014/11/02 14:01:52  rvv
    *** empty log message ***

    Revision 1.10  2014/09/21 12:22:34  rvv
    *** empty log message ***

    Revision 1.9  2014/01/04 17:02:01  rvv
    *** empty log message ***

    Revision 1.8  2013/12/04 16:18:47  rvv
    *** empty log message ***

    Revision 1.7  2013/11/27 16:24:33  rvv
    *** empty log message ***

    Revision 1.6  2013/11/09 16:21:32  rvv
    *** empty log message ***

    Revision 1.5  2013/08/07 17:13:24  rvv
    *** empty log message ***

    Revision 1.4  2013/07/31 15:42:02  rvv
    *** empty log message ***

    Revision 1.3  2013/05/12 11:14:22  rvv
    *** empty log message ***

    Revision 1.2  2013/01/30 16:51:58  rvv
    *** empty log message ***

    Revision 1.1  2012/11/25 13:05:56  rvv
    *** empty log message ***

    Revision 1.1  2012/11/21 14:58:00  rvv
    *** empty log message ***



*/

class laatstePortefeuilleWaarde extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function laatstePortefeuilleWaarde()
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
    $this->data['table']  = "laatstePortefeuilleWaarde";
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
													"list_order"=>"true",
													"categorie"=>"Recordinfo"));

		$this->addField('portefeuille',
													array("description"=>"Portefeuille",
													"default_value"=>"",
													"db_size"=>"24",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"24",
													"form_visible"=>true,
													"form_extra"=>'READONLY',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
                          "keyIn"=>"Portefeuilles",
													"categorie"=>"Recordinfo"));


		$this->addField('laatsteWaarde',
													array("description"=>"laatsteWaarde",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_money"=>2,
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rendement',
													array("description"=>"rendement",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
                          'list_extraEvalLink'=>"return '&lastTab=9&frameSrc='.base64_encode('CRM_html/plotPerf.php?id='.\$data[\$this->idField]['value']);"));

		$this->addField('rendementModel',
													array("description"=>"rendement modelportefeuille",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                                                    
 		$this->addField('beginWaarde',
													array("description"=>"Beginwaarde",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('gemVermogen',
													array("description"=>"Gem. Vermogen",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
 		$this->addField('Stortingen',
													array("description"=>"Stortingen",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
 		$this->addField('Onttrekkingen',
													array("description"=>"Onttrekkingen",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('Opbrengsten',
													array("description"=>"Opbrengsten",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('Kosten',
													array("description"=>"Kosten",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('gerealiseerd',
													array("description"=>"gerealiseerd",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));                                                    
		$this->addField('ongerealiseerd',
													array("description"=>"Onger Res YTD",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));                                                    
		$this->addField('mutatieOpgelopenRente',
													array("description"=>"Mutatie opgelopen rente",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));     
                                                                         
		$this->addField('afmstdev',
													array("description"=>"AFM stdev",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true")); 

		$this->addField('zorgMeting',
													array("description"=>"Zorgplicht",
													"default_value"=>"",
													"db_size"=>"12",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"12",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 		$this->addField('kansOpDoelvermogen',
													array("description"=>"Kans op doelvermogen",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_format"=>"%01.1f",
													"list_visible"=>true,
													"list_align"=>"right",
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
													"list_visible"=>false,
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
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
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
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

    $db=new DB();
    $query='DESC laatstePortefeuilleWaarde';
    $db->SQL($query);
    $db->Query();
    $fields=array();
    while($data=$db->nextRecord())
    {
      $fields[]=$data['Field'];
    }
    $query="SELECT Grootboekrekeningen.Grootboekrekening, Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      if(in_array($data['Grootboekrekening'],$fields))
  	  	$this->addField($data['Grootboekrekening'],
													array("description"=>$data['Omschrijving'],
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));     

    }
    
    $this->addField('omzet',
													array("description"=>"Omzet",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          
    $this->addField('omzetsnelheid',
													array("description"=>"Omzetsnelheid",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
                          "list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('benchmarkRendement',
										array("description"=>"Benchmark rendement",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('vkmDoorlKst',
										array("description"=>"Perc Doorlopende kosten VKM",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('vkmDirK',
										array("description"=>"Perc Directe kosten VKM",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('vkm',
										array("description"=>"Vergelijkende kostenmaatstaf",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('saldoGeldrek',
										array("description"=>"Saldo geldrekeningen",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('rendementQTD',
										array("description"=>"rendement QTD",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('rendementMTD',
										array("description"=>"rendement MTD",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
													"list_width"=>"150",
													"list_money"=>2,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('SignRapDatumRend',
                    array("description"=>"rendement RTD",
                          "db_size"=>"0",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_visible"=>true,
                          "form_extra"=>'onChange="javascript:checkAndFixNumber(this);"',
                          "list_width"=>"150",
                          "list_money"=>2,
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));

		$this->addField('ptfSignMethode',
										array("description"=>"PtfSignMethode",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  }
}
?>