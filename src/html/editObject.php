<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
 		File Versie					: $Revision: 1.5 $

 		$Log: editObject.php,v $
 		Revision 1.5  2011/09/14 09:26:56  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2005/11/01 11:20:08  jwellner
 		diverse aanpassingen

 		Revision 1.3  2005/04/27 08:54:03  jwellner
 		no message

 		Revision 1.2  2005/04/04 12:47:01  jwellner
 		no message

 		Revision 1.1  2005/03/18 13:29:34  jwellner
 		no message


		TODO:

		- object voor het het editten van objecten.
*/

class editObject {

	var $template = array();
	var $object;
	var $data;
	var $action;
	var $env;
	var $returnUrl;

	var $__funcvar;
	var $__appvar;
	var $output = "";

	var $_error = "";
	var $result = false;

	function editObject($object)
	{
		$this->object = $object;
	}

	function controller($action,$env = "")
	{
		$this->action = $action;
		$this->env = $env;

		// check action
		switch ($action) {
			case "edit" :
				return $this->edit();
			break;
			case "new" :
				return $this->edit();
			break;
			case "update" :
				return $this->update();
			break;
			case "delete" :
				return $this->delete();
			break;
			default :
				return false;
			break;
		}
	}

	function edit()
	{
		session_start();
		global $_SESSION;
		if(is_object($_SESSION[NAV]))	{
			$this->returnUrl = $_SESSION[NAV]->currentScript."?".$_SESSION[NAV]->currentQueryString;
		}

		$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
		$_SESSION[NAV]->returnUrl = $this->returnUrl;

		if($this->action == "edit") {
			$this->action = "update";
			if (!empty($this->env[id]))
	  		$this->object->getById($this->env[id]);

			$_SESSION[NAV]->addItem(new NavEdit("editForm",$this->object->checkAccess("edit"),$this->object->checkAccess("delete"),true));
		}
		else
		{
			$this->action = "update";
			$_SESSION[NAV]->addItem(new NavEdit("editForm", true,false,true));
		}

		//include($__funcvar[editform]);

		$form = new Form($this->object);
		$form->returnUrl = $_SESSION[NAV]->returnUrl;
		$form->updateScript = $this->__funcvar[location];
		$form->action = $this->action;

		$this->output  = template($this->__appvar["templateContentHeader"],$this->template);
		if($this->usetemplate == true)
			$this->output .= $form->template($this->__funcvar[editform]);
		else
			$this->output .= $form->getHtml();

		$this->output .= template($this->__appvar["templateRefreshFooter"],$this->template);
		session_write_close();
	}

	function update()
	{
		$fields = array_keys($this->object->data['fields']);
		for($a=0;$a < count($fields); $a++)
		{
	  	$this->object->set($fields[$a],$this->data[$fields[$a]]);
		}

		if($res = $this->object->save() && $this->data[debug] != 5)
		{
			$this->result = true;
		}
		elseif($this->data[debug] == 5)
		{
			$this->_error = "DEBUG LEVEL 5 : \n\n".var_export($this->data,true);
			$this->result = false;
		}
		else
		{
			// foutmelding
			$this->result = false;
			$this->_error = "FOUT: ".mysql_error();
		}
	}

	function delete()
	{
		$fields = array_keys($this->object->data['fields']);
		for($a=0;$a < count($fields); $a++)
		{
	  	$this->object->set($fields[$a],$this->data[$fields[$a]]);
		}

		if($res = $this->object->remove() && $this->data[debug] != 5)
		{
			$this->result = true;
		}
		elseif($this->data[debug] == 5)
		{
			$this->_error = "DEBUG LEVEL 5 : \n\n".var_export($this->data,true);
			$this->result = false;
		}
		else
		{
			// foutmelding
			$this->result = false;
			$this->_error = "FOUT: ".mysql_error();
		}
	}
}
?>