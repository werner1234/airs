<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 28 maart 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/06/06 10:10:31 $
    File Versie         : $Revision: 1.10 $
 		
    $Log: TijdelijkeBulkOrders.php,v $
    Revision 1.10  2015/06/06 10:10:31  rvv
    *** empty log message ***

    Revision 1.9  2014/05/10 13:51:28  rvv
    *** empty log message ***

    Revision 1.8  2014/01/22 10:35:31  rvv
    *** empty log message ***

    Revision 1.7  2013/10/26 15:36:08  rvv
    *** empty log message ***

    Revision 1.6  2013/09/28 14:40:56  rvv
    *** empty log message ***

    Revision 1.5  2013/09/25 15:56:56  rvv
    *** empty log message ***

    Revision 1.4  2013/09/22 15:24:43  rvv
    *** empty log message ***

    Revision 1.3  2013/08/14 15:46:29  rvv
    *** empty log message ***

    Revision 1.2  2013/06/01 16:12:14  rvv
    *** empty log message ***

    Revision 1.1  2013/05/29 15:47:33  rvv
    *** empty log message ***

    Revision 1.2  2009/04/05 09:22:52  rvv
    *** empty log message ***

    Revision 1.1  2009/03/29 14:38:46  rvv
    *** empty log message ***

 		
 	
*/

class TijdelijkeBulkOrders extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();
  
  /*
  * Constructor
  */
  function TijdelijkeBulkOrders()
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
	  global $USR;
	  $DB = new DB();
    $regelNew=$this->get('regelNr');
    $query="SELECT id,regelNr FROM TijdelijkeBulkOrders WHERE pagina='".$this->get('pagina')."' AND 
                   add_user='".mysql_real_escape_string($USR)."' order by regelNr";
 		$DB->SQL($query);
		$DB->Query();
    $toegevoegd=false;
    while($data=$DB->nextRecord())
    {
      if($toegevoegd==false)
      {
        if($regelNew<=$data['regelNr'])
        {
          if($this->get('id')==0)
            $volgorde['new']=$data['regelNr'];
          else
            $volgorde[$this->get('id')]=$data['regelNr'];
          $toegevoegd=true;
       
        }
      }
      $volgorde[$data['id']]=$data['regelNr'];
    }
    if($toegevoegd==false)
    {
            if($this->get('id')==0)
            $volgorde['new']=$data['regelNr'];
          else
            $volgorde[$this->get('id')]=$data['regelNr'];    
    }
    //if($regelNew <> count($volgorde)) //hernummering
    //{
      $n=1;
      foreach($volgorde as $id=>$regel)
      {
         $query="UPDATE TijdelijkeBulkOrders SET regelNr='$n' WHERE id='$id'";
         $DB->SQL($query);
         $DB->Query();
         $n++;
      }
    //}
    
    if($this->get('id')<>0)
    {
      unset($this->data['fields']['regelNr']);
    }
    
	 
   		($this->get("fonds")=="")?$this->setError("fonds",vt("Mag niet leeg zijn!")):true;
      ($this->get("portefeuille")=="")?$this->setError("portefeuille",vt("Mag niet leeg zijn!")):true;
      ($this->get("transactieSoort")=="")?$this->setError("transactieSoort",vt("Mag niet leeg zijn!")):true;
      ($this->get("aantal")=="")?$this->setError("aantal",vt("Mag niet leeg zijn!")):true;
      
      
//		(!is_numeric($this->get("Koers")))?$this->setError("Koers","Moet een getal zijn."):true;

    $DB = new DB();
		$query  = "SELECT id FROM Portefeuilles WHERE portefeuille = '".$this->get("portefeuille")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
		if($DB->records() < 1)
			$this->setError("portefeuille",vtb("Portefeuille %s bestaat niet.", array($this->get("portefeuille"))));

		$query  = "SELECT id FROM Fondsen WHERE fonds = '".$this->get("fonds")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
		if($DB->records() < 1)
			$this->setError("fonds",vtb("Fonds %s bestaat niet.", array($this->get("fonds"))));
    

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	  global $USR;
    $db = new DB;
    $query = "SELECT ordersNietAanmaken FROM Gebruikers WHERE Gebruiker='$USR' ";
    $db->SQL($query);
    $orderaanmaken=$db->lookupRecord();
    if($orderaanmaken['ordersNietAanmaken'] > 0)
      return false;
    else
      return true;  
  
    //return  checkAccess($type);
	}
	
	/*
  * Table definition
  */
  function defineData()
  {
    global $__ORDERvar;
    ksort($__ORDERvar['transactieSoort']);
    $this->data['name']  = "";
    $this->data['table']  = "TijdelijkeBulkOrders";
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

		$this->addField('pagina',
													array("description"=>"pagina",
													"default_value"=>"1",
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
                          
		$this->addField('fonds',
													array("description"=>"fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
                          "form_extra"=>'READONLY style="background-color:#DDDDDD"',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('portefeuille',
													array("description"=>"portefeuille",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('aantal',
													array("description"=>"aantal",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
												//	"list_format"=>"%01.6f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
 		$this->addField('ISINCode',
													array("description"=>"ISINCode",
													"default_value"=>"",
													"db_size"=>"26",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"15",
													"form_visible"=>true,
                          "form_extra"=>'onchange="select_fonds(document.editForm.ISINCode.value,600,400)"',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
 		$this->addField('client',
													array("description"=>"client",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"25",
													"form_visible"=>true,
                          "form_extra"=>'onchange="select_client(document.editForm.client.value,600,400)"',
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));                          
		$this->addField('transactieSoort',
													array("description"=>"transactieSoort",
													"default_value"=>"",
													"db_size"=>"2",
													"db_type"=>"char",
													"form_type"=>"selectKeyed",
													"form_options"=>$__ORDERvar['transactieSoort'],
													"form_select_option_notempty"=>false,
													"form_size"=>"2",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));                       
 		$this->addField('koersLimiet',
													array("description"=>"Limietkoers",
													"default_value"=>"",
													"db_size"=>"12,4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"12,4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_width"=>"100",
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

		$this->addField('checkResult',
													array("description"=>"checkResult",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));


		$this->addField('checkResultRegels',
													array("description"=>"checkResultRegels",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('statusLog',
													array("description"=>"statusLog",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"textarea",
													"form_size"=>"60",
                          "form_rows"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
 
 		$this->addField('regelNr',
													array("description"=>"regelNr",
													"default_value"=>"1",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"2",
													"form_visible"=>true,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('depotbank',
													array("description"=>"Depotbank",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"select_query"=>"SELECT Depotbank, Depotbank FROM Depotbanken ORDER BY Depotbank",
													"form_type"=>"selectKeyed",
													"form_visible"=>true,
                          "list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Depotbanken"));                       
  }
}
?>