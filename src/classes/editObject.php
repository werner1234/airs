<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/02 15:53:03 $
 		File Versie					: $Revision: 1.65 $

 		$Log: editObject.php,v $
 		Revision 1.65  2020/05/02 15:53:03  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2019/10/19 08:14:05  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2019/08/24 17:30:29  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2019/08/07 12:05:57  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2019/02/10 14:27:32  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2019/02/09 18:42:52  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2018/08/18 12:40:13  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.58  2018/07/09 06:42:59  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2018/07/08 08:19:13  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2017/12/16 18:35:22  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2017/11/22 17:05:12  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2017/04/08 18:19:02  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2017/03/16 07:19:44  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2017/03/15 16:31:08  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2016/09/21 11:43:34  cvs
 		aanpassing tbv ajaxlookup call 3856
 		
 		Revision 1.50  2016/09/04 14:39:40  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2016/04/13 16:27:43  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2016/03/20 14:38:25  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2016/01/20 08:00:45  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2015/11/11 17:17:06  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2015/11/04 16:44:18  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2015/06/19 16:30:57  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2015/01/17 18:29:45  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2015/01/03 16:06:35  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2014/11/30 13:03:37  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2014/08/09 14:55:04  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2014/07/30 15:21:21  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2014/05/14 15:27:09  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2014/03/17 07:34:07  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2014/03/16 11:15:26  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2014/02/09 11:06:14  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2014/02/02 10:45:46  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2014/01/22 15:12:00  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2014/01/22 13:36:48  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2013/10/01 07:48:16  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2013/08/24 15:45:43  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2012/10/02 16:15:03  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2012/09/13 15:56:02  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2012/01/22 13:42:54  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2011/12/31 18:15:22  rvv
 		*** empty log message ***

 		Revision 1.25  2011/12/04 12:53:49  rvv
 		*** empty log message ***

 		Revision 1.24  2011/10/27 13:22:06  rvv
 		*** empty log message ***

 		Revision 1.23  2011/10/27 13:13:07  rvv
 		*** empty log message ***

 		Revision 1.22  2011/09/18 15:47:08  rvv
 		*** empty log message ***

 		Revision 1.21  2011/08/31 14:44:46  rvv
 		*** empty log message ***

 		Revision 1.20  2011/08/11 15:40:04  rvv
 		*** empty log message ***

 		Revision 1.19  2011/05/18 16:52:38  rvv
 		*** empty log message ***

 		Revision 1.18  2011/03/30 11:56:39  cvs
 		multi database aanpassingen

 		Revision 1.17  2011/02/26 15:50:56  rvv
 		*** empty log message ***

 		Revision 1.16  2011/02/24 17:25:19  rvv
 		Dynamisch laden van pulldowns.

 		Revision 1.15  2010/11/21 13:04:55  rvv
 		*** empty log message ***

 		Revision 1.14  2010/10/09 14:47:03  rvv
 		oFCKeditor loadEditor nu met Height en Widt parameter

 		Revision 1.13  2010/08/04 15:15:21  rvv
 		*** empty log message ***

 		Revision 1.12  2010/07/25 14:39:21  rvv
 		*** empty log message ***

 		Revision 1.11  2010/01/10 10:39:08  rvv
 		*** empty log message ***

 		Revision 1.10  2009/04/11 14:21:41  rvv
 		*** empty log message ***

 		Revision 1.9  2007/11/16 11:36:15  rvv
 		*** empty log message ***

 		Revision 1.8  2007/10/09 06:16:05  cvs
 		CRM update

 		Revision 1.7  2007/08/02 14:13:02  rvv
 		*** empty log message ***

 		Revision 1.6  2006/01/18 11:58:28  jwellner
 		no message

 		Revision 1.5  2006/01/09 14:36:30  jwellner
 		form datum > SQL aangepast voor datums voor 1970

 		Revision 1.8  2006/01/09 14:29:00  jwellner
 		form datum > SQL aangepast voor datums voor 1970

 		Revision 1.7  2005/12/16 09:49:47  cvs
 		*** empty log message ***

 		Revision 1.6  2005/12/08 11:39:48  cvs
 		*** empty log message ***

 		Revision 1.5  2005/12/01 13:27:49  cvs
 		*** empty log message ***

 		Revision 1.4  2005/11/21 14:49:27  jwellner
 		calendar

 		Revision 1.3  2005/11/21 13:57:40  cvs
 		*** empty log message ***

 		Revision 1.2  2005/11/21 10:08:25  cvs
 		*** empty log message ***

 		Revision 1.1.1.1  2005/11/09 15:16:16  cvs
 		no message

 		Revision 1.3  2005/11/09 15:09:56  cvs
 		*** empty log message ***


*/

class editObject {

	var $template = array();
	var $formTemplate = "";
	var $object;
	var $data;
	var $action;
	var $returnUrl;
	var $returnError;
	var $formMethod = "GET";

	var $includeHeaderInOutput = true;

	var $__funcvar;
	var $__appvar;
	var $output = "";
  var $dbId = 1;

	var $_error = "";
	var $result = false;
	var $formVars = array();
  var $extraNavSettings = array();
  var $objects = array();

  var $JSinsert = "";

	function editObject($object, $dbId=1)
	{
		$this->object = &$object;
    $this->dbId = $dbId;
    $this->verzendDebug=false;
	}

	function addExtraObject($object)
	{
		$this->objects[] = $object;
	}
  
	function controller($action,$data = "")
	{
		$this->action = $action;
		$this->data = $data;
		if(count($_FILES)>0)
		{
			foreach($_FILES as $fieldName=>$fileData)
			{
				if($this->object->data['fields'][$fieldName]["form_type"]=='document')
				{
					if($fileData['error']==0)
					{
						$tmpArray = $fileData;
						$docdata = file_get_contents($fileData['tmp_name']);
						$tmpArray['data'] = base64_encode($docdata);
						unset($tmpArray['tmp_name']);
						unset($tmpArray['error']);
						$this->data[$fieldName] = serialize($tmpArray);
					}
				}
			}
		}

		// check action
		switch ($action)
		{
			case "edit" :
				return $this->edit();
			break;
			case "new" :
				return $this->edit();
			break;
			case "update" :
				return $this->update();
			break;
			case "updateStay" :
				$this->update();
				return $this->edit();
			break;
			case "delete" :
				return $this->delete();
			break;
			case "download" :
				return $this->download();
			break;
			default :
				return false;
			break;
		}
	}

	function edit()
	{
		session_start();
		global $_SESSION,$USR,$__appvar;

		if(is_object($_SESSION['NAV']))
		{
			if(!empty($_SESSION['NAV']->returnUrl))
				$this->returnUrl = $_SESSION['NAV']->returnUrl;
			else
				$this->returnUrl = $_SESSION['NAV']->currentScript."?".$_SESSION['NAV']->currentQueryString;
		}

		$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
		$_SESSION['NAV']->returnUrl = $this->returnUrl;
    $_SESSION['NAV']->extraSettings=$this->extraNavSettings;

		if($this->action == "edit")
		{
			$this->action = "update";
			if (!empty($this->data['id']) && $this->returnError == false)
			{
	  		$this->object->getById($this->data['id']);
        $allowEdit=$this->object->checkAccess("edit");
			  $allowDelete=$this->object->checkAccess("delete");        
	  		if($__appvar['recordLocking'])
	  		{
	  	  	$db=new DB();
	  	  	$query="DELETE FROM tableLocks WHERE (change_date < now() - interval 1 minute) OR (`table`='".$this->object->data['table']."' AND tableId='".$this->data['id']."' AND user='$USR')";
          $db->SQL($query);
          $db->Query();
          $query="SELECT user,change_date FROM tableLocks WHERE `table`='".$this->object->data['table']."' AND tableId='".$this->data['id']."'";
          if($db->QRecords($query) > 0)
          {
            $opened=$db->nextRecord();
            $allowEdit=false;
            $allowDelete=false;
            $melding=vt("Record is op ").date('d-m-Y H:i:s',db2jul($opened['change_date']))." ".vt("geopend door")." ".$opened['user'].'.';
            $navMessage=$melding." <script> alert('".addslashes($melding)."'); </script>";
            $this->object->locked=true;
          }
          else
          {
	  		    $query="INSERT INTO tableLocks SET user='$USR',`table`='".$this->object->data['table']."',tableId='".$this->data['id']."',add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
            $db->SQL($query);
            $db->Query();
          }
	  		}
			}
      else
      {            
			  $allowEdit=$this->object->checkAccess("edit");
		  	$allowDelete=$this->object->checkAccess("delete");
      }
			$_SESSION['NAV']->addItem(new NavEdit("editForm",$allowEdit,$allowDelete,true,$navMessage));
		}
		else
		{
			$this->action = "update";
			$_SESSION['NAV']->addItem(new NavEdit("editForm", $this->object->checkAccess("edit"),false,true));
		}

		//include($__funcvar[editform]);

		$this->form = new Form($this->object);
    if(count($this->objects) > 0)
    {
      foreach($this->objects as $object)
        $this->form->addExtraObject($object);
    }  
		$this->form->returnUrl = &$this->returnUrl;
		if(isset($this->skipStripAll))
		  $this->form->skipStripAll = true;
		$this->form->updateScript = &$this->__funcvar["location"];
		$this->form->action = &$this->action;
		$this->form->formVars = &$this->formVars;
		$this->form->method = &$this->formMethod;

		// dit in de editScripts doen!
		//$this->output = $this->getOutput();
	}

	function update()
	{
	  global $USR;
	  if($this->verzendDebug==true)
      logIt('Update id:('.$this->data['id'].') by '.$USR);
		if($this->data['id'])
			$this->object->getById($this->data['id']);

			$this->dataBegin = $this->object->data;

		$this->setFields();

		if($this->object->validate() == true && $this->object->checkAccess("edit") == true)
		{
			// fields are OK!
	    if($this->verzendDebug==true)
        logIt('Save id:('.$this->data['id'].') by '.$USR);
			if($res = $this->object->save() && $this->data['debug'] != 5)
			{
				$this->result = true;
				$this->updateKeys();
			}
			elseif($this->data['debug'] == 5)
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
		elseif ($this->object->validate() == true && $this->object->checkAccess("verzenden"))
		{
		  if($this->verzendDebug==true)
        logIt($USR.' Verzenden id:('.$this->data['id'].')');
      $this->verzenden();
		}
		else
		{
			// validation failed
			// call update again.
      if($this->verzendDebug==true)
        logIt($USR.' Opslaan of verzenden van id:('.$this->data['id'].') mislukt. (Formulier of rechten issue) rechten:'.$this->object->checkAccess("verzenden"));
			$this->returnError = true;
			$this->action = "edit";
			$this->edit();
		}

		if(!$this->returnError)
		{
			$db=new DB();
	  	$query="DELETE FROM tableLocks WHERE user='$USR' AND `table`='".$this->object->data['table']."' AND tableId='".$this->data['id']."'";
      $db->SQL($query);
      $db->Query();
		}
	}

	function download()
	{

		if($this->data['id'])
			$this->object->getById($this->data['id']);
		else
		{
			echo vt("Document niet aanwezig.");
			exit;
		}

		$fieldData=$this->object->data['fields'][$this->data['field']];
		$docInfo=unserialize($fieldData['value']);
		$docData=base64_decode($docInfo['data']);


		header("Content-type: ".$docInfo['type']);
		header("Content-Disposition: attachment; filename=\"".$docInfo['name']."\"");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$docInfo["size"]);
	  echo $docData;
    exit;
	}

	function delete()
	{

		$this->setFields();
		$this->dataBegin = $this->object->data;
		if($this->object->validateDelete() == true && $this->object->checkAccess("delete") == true && $this->countKeyConnections() == 0)
		{
			if($res = $this->object->remove() && $this->data['debug'] != 5)
			{
				$this->result = true;
			}
			elseif($this->data["debug"] == 5)
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
		else
		{
			// validation failed
			// call update again.
			$this->returnError = true;
			$this->action = "edit";
			$this->edit();
		}
	}

	function getTemplate()
	{
		if($this->form)
		{
			return $this->form->getTemplate();
		}
	}

	function extraTemplateVars()
	{
		$fields = array_keys($this->object->data['fields']);
		for($a=0;$a < count($fields); $a++)
		{
			// default veld controle doormiddel van database veld informatie.

			switch($this->object->data['fields'][$fields[$a]]["form_type"])
			{
				case "htmlarea" :
					$this->template['htmleditorinclude'] = "<script type=\"text/javascript\" src=\"javascript/ckeditor/ckeditor.js\"></script>";
					$this->template['htmleditorbody'] 	 = " onLoad=\"doEditorOnload();\" ";
					$this->template['htmleditorloader']  = "function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
		skin : 'v2',
		DIALOG_RESIZE_NONE : true,
		height: h,
		width: w,
		toolbar :
[
    ['Source','-','Save','NewPage','Preview','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    '/',
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['BidiLtr', 'BidiRtl'],
    ['TextColor','BGColor'],
    ['Maximize', 'ShowBlocks','-','About']
]
	});
}";
				break;
				case "htmlarea4" :
					$this->template['htmleditorinclude'] = "<script type=\"text/javascript\" src=\"javascript/ckeditor4/ckeditor.js\"></script>";
					$this->template['htmleditorbody'] 	 = " onLoad=\"doEditorOnload();\" ";
					$this->template['htmleditorloader']  = "function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
    height: h,
		width: w,
    uiColor: '#9AB8F3',
    enterMode : CKEDITOR.ENTER_BR,
    allowedContent: true,
    scayt_autoStartup:true,
    disableNativeSpellChecker:false,
    scayt_sLang: 'nl_NL'
	});
}";
          break;
        case "htmlarea4.14" :
          $this->template['htmleditorinclude'] = "<script type=\"text/javascript\" src=\"javascript/ckeditor_4.14.0/ckeditor.js\"></script>";
          $this->template['htmleditorbody'] 	 = " onLoad=\"doEditorOnload();\" ";
          $this->template['htmleditorloader']  = "function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
    height: h,
		width: w,
    enterMode: CKEDITOR.ENTER_BR,
    allowedContent: true,
    extraPlugins: 'pastebase64',
    scayt_sLang: 'nl_NL'
	});
}";
				break;
				case "calendar" :
					$this->template['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
					$kal = new DHTML_Calendar();
					$this->template['calendar'] = $kal->get_load_files_code();
				break;
				case "selectKeyed" :
				  if($this->form && $this->object->data['fields'][$fields[$a]]["select_query_ajax"])
			  		$this->template['ajaxinclude'] = $this->form->makeAjaxLookup();
				break;
			}
		}
	}

	function getOutput()
	{
		// add default template vars
		// voor oa htmlarea & calendat fields
		$this->extraTemplateVars();

    $output  = "";
    // controlleer of er een form object bestaat , gooi anders geen output uit
    if($this->form)
    {
		  if ($this->includeHeaderInOutput)
			  $output  = template($this->__appvar["templateContentHeader"],$this->template);

			if($this->usetemplate == true)
				$output .= $this->form->template($this->formTemplate);
			else
				$output .= $this->form->getHtml();



      if ($this->JSinsert <> "")
      {
        $output .= $this->JSinsert;
      }

			if ($this->includeHeaderInOutput)
      {
        $output .= template($this->__appvar["templateRefreshFooter"], $this->template);
      }
    }
		return $output;
	}

	function setFields()
	{
		$inObject = array_keys($this->object->data['fields']);
		$fields = array_keys($this->data);

		for($a=0;$a < count($fields); $a++)
		{
			// default veld controle doormiddel van database veld informatie.
			if(in_array($fields[$a],$inObject))
			{
				switch($this->object->data['fields'][$fields[$a]]["db_type"])
				{
					case "date" :
					case "datetime" :
					// extra datum check
						if(!empty($this->data[$fields[$a]]))
						{
							$dd = explode($this->__appvar["date_seperator"],$this->data[$fields[$a]]);
							if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
								$this->object->setError($fields[$a],vt("Verkeerde datum (dd-mm-jjjj)"));
              elseif(strlen($dd[2]) == 2)
              {
                $dd[2]="20".$dd[2];
                $this->data[$fields[$a]]=implode("-",$dd);
              }
              elseif(strlen($dd[2]) == 4 && $dd[2] < 100)
              {
                $dd[2]="20".substr($dd[2],2,2);
                $this->data[$fields[$a]]=implode("-",$dd);
              }
              if(intval($dd[2]) < 1900)
                $this->object->setError($fields[$a],vt("Verkeerde datum (dd-mm-jjjj)"));
						}
            if (empty($this->data[$fields[$a]]) || $this->data[$fields[$a]] == '0-0-00')
              $value = '';
            else
						  $value = formdate2db($this->data[$fields[$a]]);
					break;
					case "time" :
						if(!empty($this->data[$fields[$a]."_hour"]))
						{
							$tmpTime = $this->data[$fields[$a]."_hour"].":".$this->data[$fields[$a]."_min"];
							if(!ereg ("([0-9]{1,2}):([0-9]{1,2})", $tmpTime, $regs))
								$this->object->setError($fields[$a],vt("Verkeerde tijd (uu:mm)"));
							$value =  $tmpTime;
						}
						else
						{
							$value = $this->data[$fields[$a]];
						}
					break;
					case "int" :
						if(!empty($this->data[$fields[$a]]))
						{
							if(!is_numeric($this->data[$fields[$a]]) || strstr($this->data[$fields[$a]],"."))
								$this->object->setError($fields[$a],vt("Geen geheel getal"));
						}
						$value = $this->data[$fields[$a]];
					break;
					case "decimal" :
					case "double" :
						if(!empty($this->data[$fields[$a]]))
						{
							if(!is_numeric($this->data[$fields[$a]]))
								$this->object->setError($fields[$a],vt("Geen decimaal getal"));
						}
						$value = $this->data[$fields[$a]];
					break;
					case "varchar" :
						if(strlen($this->data[$fields[$a]]) >  $this->object->data['fields'][$fields[$a]]["db_size"])
							$this->object->setError($fields[$a],vt("Maximaal")." ".$this->object->data['fields'][$fields[$a]]["db_size"]." ".vt("karakters"));

						$value = $this->data[$fields[$a]];
					break;
					case "mediumtext" :
						if($this->object->data['fields'][$fields[$a]]["form_type"]=='document' && $this->data[$fields[$a]]=='delete_doc')
							$value='';
						else
							$value = $this->data[$fields[$a]];
						break;
					default :
						$value = $this->data[$fields[$a]];
					break;
				}
		  	$this->object->set($fields[$a],$value);
			}

		  if($this->object->data['fields'][$fields[$a]]['key_field'])
			{
			  if($this->object->data['fields'][$fields[$a]]['value'] != $this->dataBegin['fields'][$fields[$a]]['value'] && $this->dataBegin['fields'][$fields[$a]]['value'] != '')
			  {
			    $keyValue = 'key_'.$fields[$a];
			    if(!$this->data[$keyValue])
			    {
			      if($this->object->getError($fields[$a]) == '')
			        $this->object->setError($fields[$a],vt("Wijzigen sleutel?"));
			    }
			  }
			  if($this->object->data['fields'][$fields[$a]]['value'] != trim($this->object->data['fields'][$fields[$a]]['value']))
        {
          $this->object->setError($fields[$a],vt("Spatie aan het begin of eind van het veld?"));
        }
			}
		}
	}

	function findKeys($force=false)
	{
		global $__appvar;
		$updateTabel = array();
		foreach ($this->object->data['fields'] as $field=>$data)
		{
			if($data['key_field'])
			{
				if(strval($data['value']) !== strval($this->dataBegin['fields'][$field]['value']) && strval($this->dataBegin['fields'][$field]['value']) != '' || $force==true)
				{
					foreach ($__appvar['tabelObjecten'] as $tabel)
					{
						$tmpObject = new $tabel;
						foreach($tmpObject->data['fields'] as $targetField=>$fieldData)
						{
							$parts=explode(',',$fieldData['keyIn']);
							foreach($parts as $keyIn)
							{
								if($keyIn == $this->object->data['table'])
								{
									$nietToevoegen=false;
									$tmp=array('tabel'=>$tmpObject->data['table'],
														 'veld'=>$targetField,
														 'valueNew'=>$data['value'],
														 'valueOld'=>$this->dataBegin['fields'][$field]['value']);
									if(isset($tmpObject->data['fields'][$targetField]['extra_keys']))
									{
										foreach($tmpObject->data['fields'][$targetField]['extra_keys'] as $dbKey=>$dbValue)
                    {
                      if ($this->object->data['fields'][$dbKey]['value'] != $dbValue)
                      {
                        $nietToevoegen = true;
                      }
                    }
									}

									foreach($data['extra_keys'] as $key)
                  {
                    if(isset($fieldData['extraKeyLookup']) && is_array($fieldData['extraKeyLookup']) && isset($fieldData['extraKeyLookup'][$keyIn][$key]))
                    {
                       $query=str_replace('{keyvalue}',mysql_real_escape_string($this->object->data['fields'][$key]['value']),$fieldData['extraKeyLookup'][$keyIn][$key]);
                       $db=new DB();
                       $db->SQL($query);
                       $db->Query();
                       $values=array();
                       $targetKey=$key;
                       while($data=$db->nextRecord('num'))
                       {
                         $values[]=mysql_real_escape_string($data[0]);
                         if(isset($data[1]))
                           $targetKey=$data[1];
                       }
                       $tmp['extra_ins'][$targetKey]=$values;
                    }
                    else
                    {
                      $tmp['extra_keys'][$key] = $this->object->data['fields'][$key]['value'];
                    }
                  }
									if(is_array($fieldData['keyUpdateCondition']))
									{
										foreach($fieldData['keyUpdateCondition'] as $key=>$value)
										{
											if($value <> $this->object->data['fields'][$key]['value'])
												$nietToevoegen=true;
											//echo $tmpObject->data['table']."$tabel | $targetField | $field | $value | $key| $nietToevoegen (".$this->object->data['fields'][$key]['value'].")<br>\n";
										}
									}
								//	listarray($fieldData);
									if(is_array($fieldData['keyCondition']))
									{
										if($fieldData['keyCondition'])
											$tmp['extra_keys'][$fieldData['keyCondition']]=$keyIn;
									}
									elseif($fieldData['keyCondition'])
                  {
                   // listarray($fieldData); listarray($data); echo $keyIn;
                    if(isset($fieldData['keyConditionTranslation']) && is_array($fieldData['keyConditionTranslation']))
                    {
                      $tmp['extra_keys'][$fieldData['keyCondition']] = $fieldData['keyConditionTranslation'][$keyIn];
                    }
                    else
                    {
                      $tmp['extra_keys'][$fieldData['keyCondition']] = $keyIn;
                    }
                   // listarray($tmp['extra_keys']);
                  }
									if(isset($fieldData['keyUpdateWhere']))
										foreach($fieldData['keyUpdateWhere'] as $key=>$value)
											$tmp['extra_keys'][$key]=$value;
									if($nietToevoegen==false)
                  {
                    $updateTabel[$tmp['tabel'] . '.' . $tmp['veld']] = $tmp;
                  }
								}
							}
						}
					}
				}
			}

		}
		return $updateTabel;
	}

	function countKeyConnections()
	{
		$updateTabel=$this->findKeys(true);
		if (count($updateTabel) > 0)
			$db = new DB($this->dbId);

		$message='';
		foreach ($updateTabel as $update)
		{
			$extraWhere='';
			foreach($update['extra_keys'] as $key=>$value)
			{
				$extraWhere.=" AND `$key` = '$value' ";
			}
      
      foreach($update['extra_ins'] as $key=>$values)
      {
        $extraWhere.=" AND `$key` IN('".implode("','",$values)."')";
      }

			$query = "SELECT count(*) as aantal FROM ".$update['tabel']." WHERE `".$update['veld']."` = '".mysql_real_escape_string($update['valueOld'])."' $extraWhere ";
			$db->SQL($query);
			$db->Query();
			$aantal=$db->nextRecord();

			if($aantal['aantal'] > 0)
			{
				if ($message == '')
				{
					$message .= vt("Verwijderen nog niet mogelijk. Er zijn nog referenties naar dit record aanwezig.")."<br>\n";
				}
				$message .= "(" . $aantal['aantal'] . ") referenties in tabel " . $update['tabel'] . " ".vt("aanwezig").".<br>\n";
				//$exit=true;
			}
		}
		if($message<>'')
		{
			foreach($this->object->data['fields'] as $field=>$data)
			{
				if($data['key_field']==true)
				{
					$this->object->setOption($field,'noHtmlspecialchars',true);
					$this->object->setError($field, $message);
				}
			}
			return 1;
		}
    return 0;
	}

	function updateKeys($debug=false)
	{
	  global $__appvar,$USR;

		$updateTabel=$this->findKeys();

	  if (count($updateTabel) > 0)
	    $db = new DB($this->dbId);

  	$queries=array();
	  foreach ($updateTabel as $update)
	  {
	   $extraWhere='';
	    foreach($update['extra_keys'] as $key=>$value)
      {
        $extraWhere.="AND `$key` = '".mysql_real_escape_string($value)."'";
      }
      foreach($update['extra_ins'] as $key=>$values)
      {
        $extraWhere.=" AND `$key` IN('".implode("','",$values)."')";
      }
	    $query = "UPDATE ".$update['tabel']." SET `".$update['veld']."` = '".mysql_real_escape_string($update['valueNew'])."',`change_date` = NOW(), `change_user` = '$USR' WHERE `".$update['veld']."` = '".mysql_real_escape_string($update['valueOld'])."' ".$extraWhere;
			if($debug==true)
				$queries[]=$query;
			else
			{
				$db->SQL($query);
				$db->Query();
        logIt($query." ".$db->mutaties());
			}
			
	 // echo $query." ".$db->mutaties()."  <br>\n";
    //$exit=true;

	  }
//if($exit==true)
//	exit;
		if($debug==true)
			return $queries;
	}
	function verzenden()
	{
	  global $USR;
	  $db = new DB($this->dbId);
    $query = "SELECT * FROM ".$this->object->data['table']." WHERE ".$this->object->data['identity']."='".$this->object->data['fields'][$this->object->data['identity']]['value']."'";
    $db->SQL($query);
    $oldRecord=$db->lookupRecord();
    $overslaan = array('add_date','add_user','change_date','change_user','id');
    $mutaties=array();
    $tableIds=array();
    $formData=array_merge($_POST,$_GET);
    $tableIds[$this->object->data['table']]=$this->object->data['fields'][$this->object->data['identity']]['value'];
    foreach ($this->object->data['fields'] as $key=>$data)
    {
      if(!in_array($key,$overslaan))
      {
        if($data['db_type']=='date')
        {
          $oldRecord[$key]=substr($oldRecord[$key],0,10);
          $data['value']=substr($data['value'],0,10);
        }

        if($oldRecord[$key] != $data['value'] && (($oldRecord[$key] != '0000-00-00' && $data['value'] !='') || isset($formData[$key]) ))
        {
          $mutaties[$this->object->data['table']][$key]['oud']=$oldRecord[$key];
          $mutaties[$this->object->data['table']][$key]['nieuw']=$data['value'];
        }
	    }
    }
        
    if($tableIds[$this->object->data['table']] < 1)
      $tableIds[$this->object->data['table']]="9".sprintf("%05d", $_SESSION['usersession']['gebruiker']['id']).date('ymdHis');

    $counter=0;
    if($oldRecord['Vermogensbeheerder'] !='')
      $vermogensbeheerder=$oldRecord['Vermogensbeheerder'];
    elseif ($this->object->data['fields']['Vermogensbeheerder']['value'] !='')
      $vermogensbeheerder=$this->object->data['fields']['Vermogensbeheerder']['value'];
    elseif($this->verzendVermogensbeheerder != '')
      $vermogensbeheerder=$this->verzendVermogensbeheerder;
		elseif ($this->object->data['fields']['Portefeuille']['value'] !='' || $this->object->data['fields']['portefeuille']['value'] !='')
		{
		  if ($this->object->data['fields']['Portefeuille']['value'] !='')
		    $portefeuille=$this->object->data['fields']['Portefeuille']['value'];
		  else
		    $portefeuille=$this->object->data['fields']['portefeuille']['value'];
			$query="SELECT Vermogensbeheerder FROM Portefeuilles WHERE portefeuille='".mysql_escape_string($portefeuille)."'";
			$db->SQL($query);
			$vermogensbeheerderRecord=$db->lookupRecord();
			$vermogensbeheerder = $vermogensbeheerderRecord['Vermogensbeheerder'];
		}
	  if($this->verzendDebug==true)
        logIt($USR.' Verzenden: ('.count($mutaties).') mutaties gedetecteerd voor ('.$vermogensbeheerder.')');
        
    if($vermogensbeheerder <> '')
    {
      foreach ($mutaties as $tabel=>$wijziging)
      {
        foreach ($wijziging as $veld=>$waarden)
        {
          $queueDB = new DB(2);
          $query = "INSERT INTO klantMutaties SET
                    tabel = '$tabel',
                    recordId = '".$tableIds[$tabel]."',
                    veld='$veld',
                    oudeWaarde='".mysql_real_escape_string($waarden['oud'])."',
                    nieuweWaarde='".mysql_real_escape_string($waarden['nieuw'])."',
                    verwerkt='0',
                    Vermogensbeheerder='$vermogensbeheerder',
                    emailAdres='".mysql_real_escape_string($_SESSION['usersession']['gebruiker']['emailAdres'])."',
                    add_date = now(),add_user = '$USR',change_date = now() , change_user = '$USR' ";
          if($this->verzendDebug==true)
            logIt($USR.' Verzenden: ('.$query.')');
          $queueDB->SQL($query);
          if(!$queueDB->Query())
            $foutMeldingen .= vt("Verzenden van aanpassingsverzoek")." '".$waarden['oud']."' ".vt("naar")." ".$waarden['nieuw']." ".vt("mislukt").". <br>\n";
          else
          {
            $lastId=$queueDB->last_id();
            $query.=", id = $lastId";
            $DB=new DB($this->dbId);
            $DB->SQL($query);
            $DB->Query();
          }
          $counter++;
        }
      }
    }
    if($foutMeldingen)
      $this->message=$foutMeldingen;
    else
      $this->message= vt("Er is/zijn")."  ({$counter}) ".vt("mutatie(s) verzonden").".";
      
    if($this->verzendDebug==true)
        logIt($USR.' Verzenden: '.$this->message.'');
    $this->result=1;
	}
}
?>