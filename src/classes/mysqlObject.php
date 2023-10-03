<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/02/22 17:12:41 $
 		File Versie					: $Revision: 1.24 $

 		$Log: mysqlObject.php,v $
 		Revision 1.24  2017/02/22 17:12:41  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2016/03/12 17:45:46  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2014/07/21 10:05:04  cvs
 		*** empty log message ***
 		
 		Revision 1.21  2014/07/19 14:25:47  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2011/12/07 19:12:51  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2011/10/16 14:33:33  rvv
 		*** empty log message ***

 		Revision 1.18  2011/09/21 18:20:21  rvv
 		*** empty log message ***

 		Revision 1.17  2011/09/18 15:47:08  rvv
 		*** empty log message ***

 		Revision 1.16  2011/08/31 14:44:46  rvv
 		*** empty log message ***

 		Revision 1.15  2011/03/30 11:56:39  cvs
 		multi database aanpassingen

 		Revision 1.14  2009/12/20 14:28:51  rvv
 		*** empty log message ***

 		Revision 1.13  2009/04/18 15:00:21  rvv
 		*** empty log message ***

 		Revision 1.12  2007/11/16 11:37:45  rvv
 		*** empty log message ***

 		Revision 1.11  2006/04/12 07:54:23  jwellner
 		*** empty log message ***

 		Revision 1.10  2006/01/25 11:50:17  cvs
 		*** empty log message ***

 		Revision 1.9  2005/12/16 14:57:29  jwellner
 		no message


*/


/************************************************************************************************
	mysqlObject
*************************************************************************************************
	Door : Jeroen Wellner / AE-ICT bv / jw@aeict.nl

	Omschrijving:
	--------------------------
	Object voor het afhandelen van SQL voor data objecten (bv Naw.php)
	Wordt intern gebruikt.

	Functions
	--------------------------

	MysqlObject()
		omschrijving 	: constructor, maakt SQL connectie aan
		return 				: -

	getById($object)
		omschrijving 	: laad een object in met opgegeven ID
		return 				: object

	save($object)
		omschrijving 	: slaat object op in database
		return 				: database result.

	remove($object)
		omschrijving 	: verwijderd object uit database
		return 				: database result.

 	Usage:
	--------------------------
	intern gebruik voor mysqlTable.

	// object opslaan
  $db = new MySqlObject();
	return $db->save($this);

	// object ophalen uit DB
  $this->set($this->data['identity'],$id);
  $db = new MysqlObject();
	$this = $db->getById($this);

************************************************************************************************/

include_once("AE_cls_mysql.php");


class MysqlObject
{
	var $DB;
	var $user;
  var $dbId;

  function MysqlObject($dbId = 1)
  {
  	global $USR;
  	$this->user = $USR;
    $this->dbId = $dbId;
		$this->DB = new DB($this->dbId);

  }

  function getById($object)
  {
    // doe query
    // maak veld selectie
    $fields = array_keys($object->data['fields']);
    for ($a = 0; $a < count($fields); $a++)
    {
      $fieldName = $fields[$a];
   	  if ($a > 0)    $selection .= ", ";
	  $selection .= " `".$fieldName."`";
    }

    $query  = "SELECT ".$selection." FROM ".$object->data['table']." WHERE id = '".$object->data['fields'][$object->data['identity']]['value']."'";
    $this->DB->SQL($query);

    $data   = $this->DB->lookupRecord('',false);
    $fields = array_keys($object->data['fields']);
		// vul object
    for ($a = 0; $a < count($fields);    $a++)
    {
      $fieldName = $fields[$a];
      $object->set($fields[$a],$data[$fieldName]);
    }
    return true;
  }

  function save($object)
  {
    global $__appvar;
    if($object->get($object->data['identity']) > 0)
    {
      if($__appvar['logAccess'])
      {
        $query  = "SELECT * FROM ".$object->data['table']." WHERE id = '".$object->get($object->data['identity'])."'";
        $this->DB->SQL($query);
        $oldRecord=$this->DB->lookupRecord();
        $overslaan = array('add_date','add_user','change_date','change_user','id');
        $mutaties=array();
        $tableIds=array();
        $tableIds[$object->data['table']]=$object->data['fields'][$object->data['identity']]['value'];
        foreach ($object->data['fields'] as $key=>$data)
        {
          $keyLow=strtolower($key);
          $oldRecordLow=array_change_key_case($oldRecord);
          if(!in_array($key,$overslaan))
          {
            if($data['db_type']=='date' || $data['db_type']=='datetime')
            {
              $oldRecordLow[$keyLow]=substr($oldRecordLow[$keyLow],0,10);
              if($oldRecordLow[$keyLow] == '0000-00-00')
                $oldRecordLow[$keyLow]='';
              $data['value']=trim(substr($data['value'],0,10));
              if($oldRecordLow[$keyLow]=='' && $data['value']=='0000-00-00')
                $data['value']='';
            }
            if($data['db_type']=='tinyint')
            {
              if($oldRecordLow[$keyLow]=='')
                $oldRecordLow[$keyLow]=0;
              if($data['value']=='')
                $data['value']=0;
            }
            if($oldRecordLow[$keyLow] != $data['value'])
            {
              $mutaties[$this->object->data['table']][$key]['oud']=$oldRecordLow[$keyLow];
              $mutaties[$this->object->data['table']][$key]['nieuw']=$data['value'];
              $query = "INSERT INTO trackAndTrace SET
                    tabel = '".$object->data['table']."',
                    recordId = '".$object->get($object->data['identity'])."',
                    veld='$key',
                    oudeWaarde='".mysql_real_escape_string($oldRecordLow[$keyLow])."',
                    nieuweWaarde='".mysql_real_escape_string($data['value'])."',
                    add_date = now(),add_user = '".$this->user."'";
               $this->DB->SQL($query);
               $this->DB->Query();
            }
	        }
        }
      }
      $query = "UPDATE ".$object->data['table']." SET ";
    }
    else
    {
      $query = "INSERT INTO ".$object->data['table']." SET ";
    }

    $fields = array_keys($object->data['fields']);

    for ($a = 0; $a < count($fields); $a++)
    {
      $fieldName = $fields[$a];

			if(	$fieldName != "add_date" &&
					$fieldName != "add_user" &&
					$fieldName != "change_date" &&
					$fieldName != "change_user")
			{
      if ($a > 0)
        $query .= ", ";
	      $query .= " `".$fieldName."` = '".mysql_escape_string($object->get($fieldName))."'";
			}
    }

    if($object->get($object->data['identity']) > 0)
    {
      // set Change_date & Change_user info

      $query .= ", `change_date` = NOW()";
      $query .= ", `change_user` = '".$this->user."'";

      $query .= " WHERE ".$object->data['identity']." = ".$object->get($object->data['identity']);
    }
    else
    {
      // set Add_date & Add_user info
      $query .= ", change_date 	= NOW()";
      $query .= ", change_user 	= '".$this->user."'";
      $query .= ", add_date 		= NOW()";
      $query .= ", add_user 		= '".$this->user."'";
    }

    $this->DB->SQL($query);
    if($res = $this->DB->Query())
    {
     	if($object->get($object->data['identity']) <= 0)
     	{
        $object->set($object->data['identity'],$this->DB->last_id());
        if($__appvar['logAccess'])
        {
          $query = "INSERT INTO trackAndTrace SET
                    tabel = '".$object->data['table']."',
                    recordId = '".$object->get($object->data['identity'])."',
                    veld='id',
                    oudeWaarde='',
                    nieuweWaarde='".$object->get($object->data['identity'])."',
                    add_date = now(),add_user = '".$this->user."'";
          $this->DB->SQL($query);
          $this->DB->Query();    
        }
      }  
    }

    // haal laatste ID op.
    return $res;
  }

  function remove($object)
  {
    global $__appvar;
    $query = "DELETE FROM  ".$object->data['table']." WHERE ".$object->data['identity']." = '".$object->get($object->data['identity'])."'";
    $this->DB->SQL($query);
    $status=$this->DB->Query();
    if($__appvar['logAccess'])
    {
      $query = "INSERT INTO trackAndTrace SET
                    tabel = '".$object->data['table']."',
                    recordId = '".$object->get($object->data['identity'])."',
                    veld='id',
                    oudeWaarde='".$object->get($object->data['identity'])."',
                    nieuweWaarde='',
                    add_date = now(),add_user = '".$this->user."'";
      $this->DB->SQL($query);
      $this->DB->Query();    
    }
    return $status;
  }
}
?>