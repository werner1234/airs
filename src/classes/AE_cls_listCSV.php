<?
/*
    AE-ICT source module
    Author  			: $Author: cvs $
 	Laatste aanpassing	: $Date: 2009/01/06 09:10:52 $
 	File Versie			: $Revision: 1.5 $

 	$Log: AE_cls_listCSV.php,v $
 	Revision 1.5  2009/01/06 09:10:52  cvs
 	*** empty log message ***
 	

*/

include_once("mysqlList.php");

class ListCSV extends MysqlList
{
	var $csv;
	var $seperator = ",";

	function writeReport()
	{
		if($sql = $this->getSQL())
		{
			// remove limit.
			if (strstr($sql,"LIMIT" ))
			{
			  $pos = strpos($sql,"LIMIT");
			  $sql = substr($sql,0,$pos);
			}

			// maak een CSV header
			for($b=0;$b < count($this->columns); $b++)
			{
				$column = $this->columns[$b];
				if(!$this->objects[$column[objectname]] && $column[objectname] != "")
				{
					$this->objects[$column[objectname]] =  new $column[objectname]();
				}
				$column['options'] = array_merge($this->objects[$column['objectname']]->data['fields'][$column[name]],$column['options']);
				$header[] = ($column['options']['description'])?$column['options']['description']:$column['name'];
			}

			$lijst[] = $header;
			$DB = new DB();
			$DB->SQL($sql);
			$DB->Query();
			while($data = $DB->NextRecord("num"))
			{
				$lijst[] = $data;
			}

			$this->csv = generateCSV($lijst);

			return true;
		}
		else
		{
			return false;
		}
	}

	function getCSV()
	{
		if($this->writeReport())
		{
			return $this->csv;
		}
		else
		{
			return false;
		}
	}
}
?>