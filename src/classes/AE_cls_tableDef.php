<?
/*
    AE-ICT source module
    Author  			: $Author: rvv $
 	Laatste aanpassing	: $Date: 2011/12/04 12:53:49 $
 	File Versie			: $Revision: 1.1 $

 	$Log: AE_cls_tableDef.php,v $
 	Revision 1.1  2011/12/04 12:53:49  rvv
 	*** empty log message ***
 	
 	Revision 1.5  2009/01/06 09:10:52  cvs
 	*** empty log message ***


*/



class tableDef
{
  var $fields=array();
  var $table="";
  var $className="";

	function tableDef($table,$classname)
	{

	  $this->table=$table;
	  $this->classname=$classname;
	  $db=new DB();
	  $query="SHOW COLUMNS FROM `$table` ";
    $db->SQL($query);
    $db->Query();

    while ($data=$db->nextRecord())
    {
	    $this->fields[]=$data;
    }

    $this->createClass();
	}

	function createClass()
	{
   $classdef='class '.$this->classname.' extends Table
   {
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function '.$this->classname.'()
  {
    $this->defineData();
    $this->set($this->data["identity"],0);
  }

	function addField($name, $properties)
	{
		$this->data["fields"][$name] = $properties;
	}

	function checkAccess($type)
	{
		return checkAccess($type);
	}

	function validate()
	{
		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data["table"]  = "'.$this->table.'";
    $this->data["identity"] = "id";
    $this->data["logChange"] = false;

    	';

 //listarray($this->fields);

 foreach ($this->fields as $fieldData)
 {
   preg_match("/[0-9]+/", $fieldData['Type'], $size);
   if(isset($size[0]))
     $db_size=$size[0];
   else
     $db_size=10;

   preg_match("/[a-zA-Z]*/", $fieldData['Type'], $type);
   if(isset($type[0]))
     $db_type=$type[0];
   else
     $db_type="text";

   if($db_type=='datetime')
    $form_type='datum';
   else
    $form_type='text';

    if(in_array($db_type,array('int','double','tinyint','datetime')))
      $list_align='right';
    else
      $list_align='left';



   $classdef.='$this->addField("'.$fieldData['Field'].'",
													array("description"=>"'.$fieldData['Field'].'",
													"db_size"=>"'.$db_size.'",
													"db_type"=>"'.$db_type.'",
													"form_type"=>"'.$form_type.'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"'.$list_align.'",
													"list_search"=>true,
													"list_order"=>"true"));
													';
	 }

   $classdef.='
  }
}';

  eval($classdef);
	}

}


?>