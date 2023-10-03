<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/01/22 15:58:51 $
 		File Versie					: $Revision: 1.8 $

        $Log: orderLogs.php,v $
        Revision 1.8  2020/01/22 15:58:51  rvv
        *** empty log message ***

        Revision 1.7  2016/09/24 17:10:12  rvv
        *** empty log message ***

        Revision 1.6  2016/02/27 15:59:28  rvv
        *** empty log message ***

        Revision 1.5  2016/02/21 17:17:40  rvv
        *** empty log message ***

        Revision 1.4  2015/12/06 18:03:19  rvv
        *** empty log message ***

        Revision 1.3  2015/08/26 15:44:29  rvv
        *** empty log message ***

        Revision 1.2  2015/07/15 11:53:54  rvv
        *** empty log message ***

        

*/

/**
 * Toevoegen van een log 
 * $orderLogs->addToLog($orderId, $fixOrderId, $message, $datetime)
 */
class orderLogs extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function orderLogs()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
		    return true;
	}

	function validate()
	{
	  global $__appvar;
   
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
  
  /**
   * Add data to order Log
   * @global type $USR
   * @param type $orderId
   * @param type $fixOrderId
   * @param type $message
   * @param type $datetime
   * @return type
   */
  function addToLog($orderRecordId = '', $fixRecordId = '', $message = '', $datetime = '',$user='',$logLevel=5,$timeOffset='')
  {
    global $USR;
    if($user=='')
      $user=$USR;
   // echo "$orderRecordId = '', $fixRecordId = '', $message = '', $datetime = '',$user=''";exit;
    if($orderRecordId=='' && $fixRecordId=='')
      return -1;

    if ( empty ($datetime) ) {$datetime = date('Y-m-d H:i:s');}
    $db = new db();
    $sql = "INSERT INTO `" . $this->data['table'] . "` SET 
      `fixRecordId` = '" . $fixRecordId . "',
      `orderRecordId` = '" . $orderRecordId . "',
      `logLevel` = '" . $logLevel . "',
      `message` = '" .mysql_real_escape_string($message) . "',
      `change_user` = '" . $user . "', 
      `change_date` = '" . $datetime . "', 
      `timeOffset` = '" . $timeOffset . "', 
      `add_user` = '" . $user . "', 
      `add_date` = now()";
    //  echo "$sql <br>\n";
    return $db->executeQuery($sql);
  }

  function addToBulkLog($bulkorderRecordId = '', $message = '', $datetime = '',$user='',$logLevel=5)
  {
    global $USR;
    if($user=='')
      $user=$USR;
   // echo "$orderRecordId = '', $fixRecordId = '', $message = '', $datetime = '',$user=''";exit;
    if($bulkorderRecordId=='')
      return -1;

    if ( empty ($datetime) ) {$datetime = date('Y-m-d H:i:s');}
    $db = new db();
    $sql = "INSERT INTO `" . $this->data['table'] . "` SET 
      `bulkorderRecordId` = '" . $bulkorderRecordId . "',
      `message` = '" . $message . "',
      `logLevel` = '" . $logLevel . "',
      `change_user` = '" . $user . "', 
      `change_date` = '" . $datetime . "', 
      `add_user` = '" . $user . "', 
      `add_date` = now()";
    //  echo "$sql <br>\n";
    return $db->executeQuery($sql);
  }  
 
  
  function getForOrder ($orderRecordId,$fix=false)
  {
    $db= new DB();
    
    if($fix==false)
      $filterField="orderRecordId";
    else
      $filterField="fixRecordId";
      
    $query="SELECT orderLogs.change_date,orderLogs.add_user,orderLogs.message,fixOrders.orderid as fixOrderId, orderLogs.timeOffset
    FROM orderLogs LEFT JOIN fixOrders ON orderLogs.fixRecordId=fixOrders.id 
    WHERE $filterField='".$orderRecordId."'
    ORDER BY orderLogs.id DESC";
    $db->executeQuery($query);
    $tmp=array();
    while($data=$db->nextRecord())
      $tmp[]=$data;
    return $tmp;
    //return $this->parseBySearch(array('orderRecordId' => $orderRecordId), 'all', ' ORDER BY `add_date` DESC', -1);
  }
  

	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar;
    $this->data['table']  = "orderLogs";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"
													));
		$this->addField('logLevel',
													array("description"=>"logLevel",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_size"=>"4",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));
    $this->addField('fixRecordId',
													array("description"=>"fixRecordId",
													"db_size"=>"25",
													"db_type"=>"int",
													"form_size"=>"11",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150","list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"key_field"=>true));
    /*
    $this->addField('orderIdRecord',
													array("description"=>"orderIdRecord",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_size"=>"25",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150","list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													));
    */
    $this->addField('bulkorderRecordId',
													array("description"=>"orderIdRecord",
													"db_size"=>"16",
													"db_type"=>"varchar",
													"form_size"=>"25",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150","list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													));
		$this->addField('message',
													array("description"=>"Bericht",
													"db_size"=>"50",
													"db_type"=>"tinytext",
													"form_size"=>"50",
													"form_type"=>"text",
													"form_visible"=>true,"list_width"=>"150","list_width"=>"150",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		
  }
}