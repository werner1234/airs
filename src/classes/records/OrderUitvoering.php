<?php
/*
    AE-ICT CODEX source module versie 1.6, 19 september 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2013/03/30 12:27:02 $
    File Versie         : $Revision: 1.6 $

    $Log: OrderUitvoering.php,v $
    Revision 1.6  2013/03/30 12:27:02  rvv
    *** empty log message ***

    Revision 1.5  2012/03/11 17:15:23  rvv
    *** empty log message ***

    Revision 1.4  2012/01/22 13:43:13  rvv
    *** empty log message ***

    Revision 1.3  2011/12/21 19:16:40  rvv
    *** empty log message ***

    Revision 1.2  2009/10/07 16:09:27  rvv
    *** empty log message ***

    Revision 1.1  2009/10/07 10:04:41  rvv
    *** empty log message ***



*/

class OrderUitvoering extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function OrderUitvoering()
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

	  $db= new DB();
    $query="SELECT Orders.aantal FROM Orders WHERE Orders.orderid = '".$_GET['orderid']."'";
    $db->SQL($query);
    $orderData=$db->lookupRecord();

    $query="SELECT SUM(uitvoeringsAantal) as aantal FROM OrderUitvoering WHERE orderid = '".$_GET['orderid'] ."' AND id <> '".$this->get('id')."'";
    $db->SQL($query);
    $uitvoeringsData = $db->lookupRecord();

    $toAdd=$orderData['aantal']-$uitvoeringsData['aantal'];

    if($this->get('uitvoeringsAantal') > $toAdd)
    {
      $this->set('uitvoeringsAantal',$toAdd);
      $this->setError('uitvoeringsAantal',vt("UitvoeringsAantal > orderaantal. UitvoeringsAantal aangepast."));
    }

		$valid = ($this->error==false)?true:false;
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
    $this->data['table']  = "OrderUitvoering";
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

		$this->addField('orderid',
													array("description"=>"orderid",
													"default_value"=>"",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"16",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('uitvoeringsAantal',
													array("description"=>"uitvoeringsAantal",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.2f",
													"list_numberformat"=>4,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
                          

		$this->addField('uitvoeringsDatum',
													array("description"=>"uitvoeringsDatum",
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

		$this->addField('uitvoeringsPrijs',
													array("description"=>"uitvoeringsPrijs",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.6f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('nettokoers',
													array("description"=>"nettokoers",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_format"=>"%01.6f",
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
		$this->addField('opgelopenrente',
													array("description"=>"opgelopenrente",
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