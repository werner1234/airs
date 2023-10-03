<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
	Laatste aanpassing	: $Date: 2018/07/09 06:42:59 $
	File Versie					: $Revision: 1.58 $

 		$Log: formObject.php,v $
 		Revision 1.58  2018/07/09 06:42:59  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2018/07/08 08:19:13  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2018/06/27 10:45:16  rm
 		Standaard waarde voor autocomplete
 		
 		Revision 1.54  2017/04/08 18:19:02  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2016/11/05 17:53:11  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2016/05/21 19:01:01  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2016/04/17 16:49:47  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2016/04/16 17:09:33  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2016/01/30 16:19:39  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2015/11/30 16:17:28  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2015/11/29 13:06:45  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2015/11/07 16:40:37  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2015/11/04 16:44:18  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2015/07/24 14:50:14  rm
 		toevoegen hidden field
 		
 		Revision 1.43  2015/06/24 14:43:18  rm
 		Orders v2
 		
 		Revision 1.42  2015/06/20 06:07:38  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2015/06/20 06:01:30  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2015/06/19 16:30:57  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2015/06/16 10:23:45  rm
 		toevoegen form id en naam
 		
 		Revision 1.38  2015/06/05 15:00:38  rm
 		id toevoegen aan form
 		
 		Revision 1.37  2015/04/11 17:04:32  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2015/01/17 18:29:45  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2014/12/17 11:40:20  rm
 		autocomplete default input value
 		
 		Revision 1.34  2014/12/10 16:07:46  rm
 		Toevoegen autocomplete en jqueryui datepicker
 		
 		Revision 1.33  2014/01/22 13:36:48  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2013/11/27 16:25:05  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2013/07/24 15:42:51  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2012/08/05 10:41:34  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2012/06/20 18:07:57  rvv
 		*** empty log message ***

 		Revision 1.28  2011/10/23 13:18:43  rvv
 		*** empty log message ***

 		Revision 1.27  2011/08/31 14:44:46  rvv
 		*** empty log message ***

 		Revision 1.26  2011/07/23 17:16:48  rvv
 		*** empty log message ***

 		Revision 1.25  2011/06/02 14:59:15  rvv
 		*** empty log message ***

 		Revision 1.24  2011/04/27 17:50:44  rvv
 		*** empty log message ***

 		Revision 1.23  2011/02/26 15:50:56  rvv
 		*** empty log message ***

 		Revision 1.22  2011/02/24 17:25:19  rvv
 		Dynamisch laden van pulldowns.

 		Revision 1.21  2010/07/25 14:39:21  rvv
 		*** empty log message ***

 		Revision 1.20  2010/01/09 12:58:53  rvv
 		*** empty log message ***

 		Revision 1.19  2009/12/20 14:28:51  rvv
 		*** empty log message ***

 		Revision 1.18  2009/10/17 12:49:55  rvv
 		*** empty log message ***

 		Revision 1.17  2007/10/09 06:16:05  cvs
 		CRM update

 		Revision 1.16  2007/08/02 14:13:02  rvv
 		*** empty log message ***

 		Revision 1.15  2006/05/12 08:23:21  jwellner
 		*** empty log message ***

 		Revision 1.17  2006/02/13 15:35:25  jwellner
 		*** empty log message ***

 		Revision 1.16  2006/01/23 16:12:04  jwellner
 		no message

 		Revision 1.15  2006/01/23 09:11:44  jwellner
 		DEB layer bugfix, date bugfix

 		Revision 1.12  2006/01/19 15:19:40  jwellner
 		bug in formobject en editObject

 		Revision 1.11  2006/01/09 14:29:00  jwellner
 		form datum > SQL aangepast voor datums voor 1970

 		Revision 1.10  2005/12/16 09:49:47  cvs
 		*** empty log message ***

 		Revision 1.9  2005/12/14 08:33:16  cvs
 		*** empty log message ***

 		Revision 1.8  2005/11/30 08:48:54  cvs
 		*** empty log message ***

 		Revision 1.7  2005/11/28 07:31:48  cvs
 		*** empty log message ***

 		Revision 1.6  2005/11/23 09:38:36  jwellner
 		no message

 		Revision 1.5  2005/11/21 14:49:27  jwellner
 		calendar

 		Revision 1.4  2005/11/21 13:57:31  cvs
 		*** empty log message ***

 		Revision 1.3  2005/11/21 12:30:17  jwellner
 		formobject

 		Revision 1.2  2005/11/17 08:05:52  cvs
 		*** empty log message ***


*/

class Form
{
  // action
  var $do;
  var $target;
  var $action;
  var $method = "GET";

  var $object;
  
  var $formName;
  var $formId;
  var $objects = array();

  function Form($object)
  {
  	$this->object = &$object;
  }
  
  function addExtraObject($object)
  {
    $this->objects[] = $object;
    
  }

  function getHtml()
  {
  	// return form header
		if($this->method=='FILE')
		{
			$method="enctype=\"multipart/form-data\" method=\"POST\" ";
		}
		else
		{
			$method="method=\"".$this->method."\" ";
		}
		$html = "<form  id=\"".( isset($this->object->formId) && !empty($this->object->formId) ? $this->object->formId : '' )."\" name=\"".( isset($this->object->formName) && !empty($this->object->formName) ? $this->object->formName : 'editForm' )."\" action=\"".$this->updateScript."\" $method >\n";
		$html .= "<div class=\"form\">\n";
		$html .= "<input type=\"hidden\" name=\"action\" value=\"".$this->action."\">\n";
		$html .= "<input type=\"hidden\" name=\"updateScript\" value=\"".$this->updateScript."\">\n";
		$html .= "<input type=\"hidden\" name=\"returnUrl\" value=\"".$this->returnUrl."\">\n";

  	// walk trough object fields
		$fields = array_keys($this->object->data['fields']);
		for($a=0;$a < count($fields); $a++)
		{

			if(	$fields[$a] != "change_date" &&
					$fields[$a] != "change_user" &&
					$fields[$a] != "add_date" &&
					$fields[$a] != "add_user")
			{
				if ($this->object->data['fields'][$fields[$a]]['form_visible'])
				{
					$html .= "<div class=\"formblock\">\n";
	  			$html .= "<div class=\"formlinks\">".vt($this->object->getDescription($fields[$a]))." </div>\n";
					$html .= "<div class=\"formrechts\">\n";
				}
				$html .= $this->makeInput($fields[$a]);
				if ($this->object->data['fields'][$fields[$a]]['form_visible']) {
					// display error message (if any)
					if($this->object->error == true)
					{
						$html .= "<span class=\"error_message\">".$this->object->getError($fields[$a])."</span>\n";
					}
					$html .= "</div>\n";
					$html .= "</div>\n\n";
				}
			}

			if($this->object->data['fields'][$fields[$a]]['form_type'] == "htmlarea")
			{
				$editorhtml .= $this->makeEditor($fields[$a],$this->object->data['fields'][$fields[$a]]['form_rows']*50,$this->object->data['fields'][$fields[$a]]['form_size']*10);
			}
		}

		$html .= "<div class=\"formblock\">\n";
		$html .= "<div class=\"formlinks\">&nbsp;</div>\n";
		$html .= "<div class=\"formrechts\">\n";
		if($this->object->get("change_user") <> '')
		  $html .= $this->object->get("change_user")." ".dbdate2form($this->object->get("change_date"));
		else
		  $html .= $this->object->get("add_user")." ".date("d-m-Y H:i:s",db2jul($this->object->get("add_date")));
		$html .= "</div>\n";
		$html .= "</div>\n\n";
		// return form footer
		$html .= "</form></div>\n";
		if($editorhtml)
		{
			$html .= "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			$html .= "function doEditorOnload()\n {\n";
			$html .= $editorhtml;
			$html .= "}\n";
			$html .= "</script>";
		}

		return $html;
  }

  function getTemplate()
  {
  	// return form header
		$html = "<form name=\"editForm\" action=\"{updateScript}\" method=\"{method}\" >\n";
		$html .= "<div class=\"form\">\n";
		$html .= "<input type=\"hidden\" name=\"action\" value=\"{action}\">\n";
		$html .= "<input type=\"hidden\" name=\"updateScript\" value=\"{updateScript}\">\n";
		$html .= "<input type=\"hidden\" name=\"returnUrl\" value=\"{returnUrl}\">\n";

  	// walk trough object fields
		$fields = array_keys($this->object->data['fields']);
		for($a=0;$a < count($fields); $a++)
		{
			if(	$fields[$a] != "change_date" &&
					$fields[$a] != "change_user" &&
					$fields[$a] != "add_date" &&
					$fields[$a] != "add_user")
			{
				if ($this->object->data['fields'][$fields[$a]]['form_visible'])
				{
					$html .= "<div class=\"formblock\">\n";
	  			$html .= "<div class=\"formlinks\"><label for=\"".$fields[$a]."\">{".$fields[$a]."_description}</label> </div>\n";
					$html .= "<div class=\"formrechts\">\n";
				}
				$html .= "{".$fields[$a]."_inputfield}";
				if ($this->object->data['fields'][$fields[$a]]['form_visible'])
				{
					// display error message (if any)
						$html .= " {".$fields[$a]."_error}\n";
					$html .= "</div>\n";
					$html .= "</div>\n\n";
				}
			}
			if($this->object->data['fields'][$fields[$a]]['form_type'] == "htmlarea" ||
         $this->object->data['fields'][$fields[$a]]['form_type'] == "htmlarea4" ||
         $this->object->data['fields'][$fields[$a]]['form_type'] == "htmlarea4.14")
			{
				$editorhtml .= $this->makeEditor($fields[$a],$this->object->data['fields'][$fields[$a]]['form_rows']*50,$this->object->data['fields'][$fields[$a]]['form_size']*10);
			}
		}

		$html .= "<div class=\"formblock\">\n";
		$html .= "<div class=\"formlinks\">&nbsp;</div>\n";
		$html .= "<div class=\"formrechts\">\n";
		$html .= "{change_user_value} {change_date_value}";
		$html .= "</div>\n";
		$html .= "</div>\n\n";

		// return form footer
		$html .= "</form></div>\n";
		if($editorhtml)
		{
			$html .= "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			$html .= "function doEditorOnload()\n {\n";
			$html .= $editorhtml;
			$html .= "}\n";
			$html .= "</script>";
		}
		return $html;
  }

  function makeEditor($input,$h,$w)
  {
		$html = " loadEditor('".$input."',$h,$w);\n ";

		return $html;
  }

  function makeAjaxLookup()
  {
    $html = 'var ajax = new Array();
function getAjaxWaarden (sel,formExtra,Veld)
{
  if(document.getElementById(Veld).options.length < 10)
  {
    var oldValue = document.getElementById(Veld).value;
    if(sel.length>0){
	  	var index = ajax.length;
	  	ajax[index] = new sack();
	 	  ajax[index].element = Veld;
		  ajax[index].requestFile = \'lookups/ajaxLookup.php?module=queryLookups&query=\'+sel;	// Specifying which file to get
	  	ajax[index].onCompletion = function(){ setAjaxWaarden(index,Veld,oldValue,formExtra) };	// Specify function that will be executed after file has been found
	  	ajax[index].onError = function(){ alert(\'Ophalen velden mislukt.\') };
	  	ajax[index].runAJAX();		// Execute AJAX function
    }
	}
}
function setAjaxWaarden(index,veld,oldValue,formExtra)
{
 	var	Waarden = ajax[index].response;
	var elements = Waarden.split(\'\n\');
	var useDiv=0;
	if(document.getElementById("div_"+veld)){useDiv=1};
 	if(elements.length > 1)
 	{
 	  var item=\'\';
	  if(useDiv)
	  {
	    var div_a =\'<select name="\'+veld+\'\" style="width:200px" \'+formExtra+\' >\';
	    div_a += \'<option value="" >---</option>\';
	    var selectedA=\'\';
	  }
	  else
	  {
	    document.getElementById(veld).options.length=0;
 	    AddName(\'editForm\',veld,\'---\',\'\');
	  }
    for(var i=0;i<elements.length;i++)
 	  {
 	    var fields = elements[i].split(\'\t\');
 	    if(elements[i] != \'\')
 	    {
 	      if(useDiv)
	      {
	   	    if(fields[0]==oldValue){selectedA="selected";}else{selectedA=""};
          div_a += \'<option value="\' + fields[0] + \'" \' + selectedA + \'>\' + fields[1] + \'</option>\';
	      }
	      else
	      {
          AddName(\'editForm\',veld,fields[0],fields[1]);
	      }
      }
    }
 	}
 	if(useDiv)
 	{
 	   div_a += "</select>";
     document.getElementById("div_"+veld).innerHTML=div_a;
 	}
 	else
 	{
    document.getElementById(veld).value = oldValue;
 	}
}
function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}';
    return $html;
  }

  function makeInput($field,$object)
  {
		// haal value op
    if(!is_object($object))
      $object=$this->object;
      
		$printdata = $object->get($field);

		if($object->data['fields'][$field][form_type] == "datum" || $object->data['fields'][$field][form_type] == "calendar" || $object->data['fields'][$field][form_type] == "date" )
		{
			$printdata = dbdate2form($printdata);
		}
    
    if( isset ($object->data['fields'][$field]['validate']['required']) && $object->data['fields'][$field]['validate']['required'] === true )
		{
      if ( ! isset ($object->data['fields'][$field]['form_class']) || empty ($object->data['fields'][$field]['form_class']) )
      {
        $object->data['fields'][$field]['form_class'] = 'isRequired';
      } else {
        $object->data['fields'][$field]['form_class'] = $object->data['fields'][$field]['form_class'] . ' isRequired';
      }

		}

		if($object->data['fields'][$field]['form_type'] == "time")
		{
			if($printdata)
			{
				$datumarray = explode(":",$printdata);
				$printdata =	$datumarray[0].":".$datumarray[1];
			}
		}

		if (!$object->data['fields'][$field]['form_visible'])
		{
			$input = "<input class=\"".$object->data['fields'][$field]['form_class']."\" type=\"hidden\"  value=\"".htmlspecialchars($printdata)."\" name=\"".$field."\" >\n";
		}
		else
		{
			if($object->data['fields'][$field]['beperkt']==true)
			  $beperkt='DISABLED';
			else
  			$beperkt='';

			// formatering van velden
			if($object->data['fields'][$field]['form_format'])
			{
				$printdata = sprintf($object->data['fields'][$field]['form_format'], $printdata);
			}

			if($object->data['fields'][$field]['key_field'])
			{
				$keyinput  = "<input type=\"hidden\" name=\"key_".$field."\" value=\"0\">";
				$keyinput .= "<input class=\"".$object->data['fields'][$field]['form_class']."\" type=\"checkbox\"  name=\"key_".$field."\" id=\"key_".$field."\" value=\"1\" >";
			}
			else
			  $keyinput = '';

			switch($object->data['fields'][$field]['form_type']) {
				case "textarea" :
				case "htmlarea" :
        case "htmlarea4" :
        case "htmlarea4.14" :
					$type = "text";
					$input = "<textarea $beperkt class=\"".$object->data['fields'][$field]['form_class']."\"  cols=\"".$object->data['fields'][$field]['form_size']."\"  rows=\"".$object->data['fields'][$field]['form_rows']."\" name=\"".$field."\" id=\"".$field."\" ".$object->data['fields'][$field]['form_extra'].">".htmlspecialchars($printdata)."</textarea>\n";
				break;
				case "memo" :
					$type = "text";
					$input = "<input $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"".$type."\" size=\"".$object->data['fields'][$field]['form_size']."\" value=\"".htmlspecialchars($printdata)."\" name=\"".$field."\" ".$object->data['fields'][$field]['form_extra'].">\n";
				break;
				case "calendar" :
          
          /** Controlleer of we een AIRSdatepicker class hebben **/
          if ( isset ($object->data['fields'][$field]['form_class']) && strpos ($object->data['fields'][$field]['form_class'], 'AIRSdatepicker') !== false ) {
            if ( isset($object->data['fields'][$field]['date_format']) && ! empty($object->data['fields'][$field]['date_format']) ) {
              $printdata = date($object->data['fields'][$field]['date_format'], strtotime($printdata));
            }
            $input = "<input $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"text\" size=\"".( ! isset ($object->data['fields'][$field]['form_size']) ? '11' : $object->data['fields'][$field]['form_size'])."\" value=\"".htmlspecialchars($printdata)."\" name=\"".$field."\" id=\"".$field."\" ".$object->data['fields'][$field]['form_extra'].">\n";
          }
          /** geen AIRSdatepicker class, toon oude calendar **/
          else {
            $kal = new DHTML_Calendar();
            $inp = array ('name' =>$field,'value' =>$printdata,'size'  => ( ! isset ($object->data['fields'][$field]['form_size']) ? '11' : $object->data['fields'][$field]['form_size']));
            //set calendar to jquery ui
            $calendarOptions = array(
              'ui_calendar' => false
            );

            if ( isset ($object->data['fields'][$field]['form_class']) )
            {
              $calendarOptions['class'] = $object->data['fields'][$field]['form_class'];
              //simple if class isset should be a check for specific calandar class
              if ( strpos ($object->data['fields'][$field]['form_class'], 'AIRSdatepicker') !== false )
              {
                $calendarOptions['ui_calendar'] = true;
              }
            }

            $input = $kal->make_input_field($calendarOptions,$inp,$object->data['fields'][$field]['form_extra'].' '.$beperkt, $field);
          }
					break;
				case "date" :
				case "datum" :
					$type = "text";
					$input = "<input $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"".$type."\" size=\"10\" value=\"".htmlspecialchars($printdata)."\" name=\"".$field."\" ".$object->data['fields'][$field]['form_extra'].">\n";
				break;
				case "document" :
					$type = "file";
					if($printdata=='')
				  	$input = "<input $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"".$type."\" size=\"10\" value=\"\" name=\"".$field."\" ".$object->data['fields'][$field]['form_extra']."> ";
					$docData=unserialize($object->data['fields'][$field]['value']);
					if($docData['size']<1024)
						$size = '(<1KB)';
					elseif($docData['size']<1024*1024)
						$size='('.round($docData['size']/1024).'KB)';
					else
						$size='('.round($docData['size']/1024/1024).'MB)';
					if($printdata<>'')
					{
						$input .= "<a href=\"" . str_replace('{id}', $object->data['fields']['id']['value'], $object->data['fields'][$field]['downloadLink']) . "\" target=\"_blank\" /> <b>download " . $docData['name'] . " $size </b></a>\n";
						$input .= "<input type=\"checkbox\"  name=\"".$field."\" id=\"".$field."\" value=\"delete_doc\"> verwijderen?";
					}
				break;
				case "checkbox" :
				  $checkValue = (strtoupper($object->data['fields'][$field]['form_extra']) == "DISABLED")?$printdata:"0";
					if($beperkt=='')
					  $input  = "<input type=\"hidden\" name=\"".$field."\" value=\"".$checkValue."\">";
					$input .= "<input $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"checkbox\"  name=\"".$field."\" id=\"".$field."\" ".(($printdata==1)?"checked":"")." value=\"1\" ".$object->data['fields'][$field]['form_extra'].">";
					break;
				case "radio" :
					if(is_array($object->data['fields'][$field]['form_options']))
					{
						reset($object->data['fields'][$field]['form_options']);
 						while (list($key, $value) = each($object->data['fields'][$field]['form_options']))
 						{
							$input .= "<input $beperkt type=\"radio\" name=\"$field\" value=\"".$key."\" ".((strtolower($printdata)==strtolower($key))?"checked":"")." ".$object->data['fields'][$field]['form_extra'].">".$value." <br>\n";
						}
					}
					break;
				case "select" :
					$input = "<select $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"select\"  name=\"".$field."\" id=\"".$field."\" ".$object->data['fields'][$field]['form_extra'].">\n";
					if (!$object->data['fields'][$field]['form_select_option_notempty'])
					  $input .= "<option value=\"\"> --- </option>";
          
          if(!is_array($object->data['fields'][$field]['form_options']))
            $object->data['fields'][$field]['form_options']=array($printdata);
          elseif(!in_array($printdata,$object->data['fields'][$field]['form_options']) && $printdata <> '')
            $object->data['fields'][$field]['form_options'][]=$printdata;

          $selected='';
          foreach($object->data['fields'][$field]['form_options'] as $key=>$value)
 					{
 					  if(strval($printdata)===strval($value))
              $selected='selected';
            else
              $selected='';
             
 						$input .= "<option value=\"".$value."\" ".$selected." >".$value."</option>\n";
					}
					$input .= "</select>";
					break;
				case "time" :
          $input = '<input type="hidden" name="'.$field.'" value="">';
					$input .= "<select $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"select\"  name=\"".$field."_hour\" ".$object->data['fields'][$field]['form_extra'].">\n";
					for($hourCount = 0; $hourCount < 24; $hourCount++)
					{
							$input .= "<option value=\"".$hourCount."\" ".(($datumarray[0]==$hourCount)?"selected":"").">".uitnullen($hourCount,2)."</option>\n";
					}
					$input .= "</select>";
					$input .= ":<select class=\"".$object->data['fields'][$field]['form_class']."\" type=\"select\"  name=\"".$field."_min\" ".$object->data['fields'][$field]['form_extra'].">\n";

					if($object->data['fields'][$field]['time_interval'])
					{
						$interval = $object->data['fields'][$field]['time_interval'];
					}
					else
					{
						$interval = 1;
					}

					for($minCount = 0; $minCount < 60; $minCount+=$interval)
					{
							$input .= "<option value=\"".$minCount."\" ".(($datumarray[1]==$minCount)?"selected":"").">".uitnullen($minCount,2)."</option>\n";
					}
					$input .= "</select>";
					break;
				case "selectKeyed" :
				  // als select_query gevuld dan waarden uit database halen
				  if ($object->data['fields'][$field]['select_query_ajax'])
				  {
				    $DBc = New DB();
				    $query=str_replace("{value}",$printdata,$object->data['fields'][$field]['select_query_ajax']);
				    $DBc->SQL($query);
				    $DBc->Query();
				    while($rec = $DBc->nextRecord("num"))
				      $_tmpArray[$rec[0]] = $rec[1];
				    if (is_array($_tmpArray))
				      $object->data['fields'][$field]['form_options'] = $_tmpArray;
				    if($object->data['fields'][$field]['select_query'])
				      $onchange='onfocus="javascript:getAjaxWaarden(\''.urlencode(base64_encode(gzcompress($object->data['fields'][$field]['select_query']))).'\',\''.addslashes($object->data['fields'][$field]['form_extra']).'\',this.name)"';
				    $pre_input='<div id="div_'.$field.'" style="display:inline-block" >';
				    $post_input='</div>';
				  }
				  elseif ($object->data['fields'][$field]['select_query'])
				  {
				    $pre_input='';
				    $post_input='';
				    $DBc = New DB();
				    $DBc->SQL($object->data['fields'][$field]['select_query']);
				    $DBc->Query();
				    while($rec = $DBc->nextRecord("num"))
				    {
				      $_tmpArray[$rec[0]] = $rec[1];
				    }
				    if (is_array($_tmpArray))
				      $object->data['fields'][$field]['form_options'] = $_tmpArray;
				  }
					$input = "$pre_input<select $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"select\"  name=\"".$field."\" $onchange id=\"".$field."\" ".$object->data['fields'][$field]['form_extra'].">\n";
					if (!$object->data['fields'][$field]['form_select_option_notempty'])
					  $input .= "<option value=\"\"> --- </option>";

          if(!is_array($object->data['fields'][$field]['form_options']) && $printdata <> '')
            $object->data['fields'][$field]['form_options']=array($printdata=>$printdata);


					if(is_array($object->data['fields'][$field]['form_options']))
					{
					  if($object->data['fields'][$field]['default_value'] <> '' && $printdata=='' && $object->data['fields']['id']['value']==0)
              $printdata=$object->data['fields'][$field]['default_value'];
              
            foreach($object->data['fields'][$field]['form_options'] as $key=>$value)
  					{
  					  if(strval($printdata)===strval($key))
                $selected='selected';
              else
                $selected='';
							$input .= "<option value=\"".$key."\" ".$selected.">".$value."</option>\n";
						}
					}
					$input .= "</select>$post_input";
					break;
				case 'autocomplete':
					$fieldData = $object->data['fields'][$field];
					$fieldValue = $printdata;
					if ( isset($fieldData['form_value']) && ! empty ($fieldData['form_value']) ) {
						$fieldValue = $fieldData['form_value'];
					}
					$input = "<input type=\"text\" $beperkt name=\"".$field."\" class=\"".$object->data['fields'][$field]['form_class']."\" size=\"".$fieldData['form_size']."\" id=\"" .$field  . "\" value=\"".htmlspecialchars($fieldValue)."\"  ".$object->data['fields'][$field]['form_extra'].">\n";
					break;
        case 'hidden':
          $fieldData = $object->data['fields'][$field];
          $input = '<input type="hidden" name="'.$field.'" class="'.$fieldData['form_class'].'" id="'.$field.'" value="'.htmlspecialchars($fieldData['form_value']).'" '.$fieldData['form_extra'].'>'."\n";
          
          break;
				default  :
					$type = "text";
					$input = "<input $beperkt class=\"".$object->data['fields'][$field]['form_class']."\" type=\"".$type."\"  size=\"".$object->data['fields'][$field]['form_size']."\" value=\"".($printdata)."\" name=\"".$field."\" id=\"".$field."\" ".$object->data['fields'][$field]['form_extra'].">\n";
				break;
			}
			$input .= $keyinput;
		}
		return $input;
  }

	function template($template)
	{
	  if(!is_file($template))
    {
      $data = $template;
    }
	  else
    {
      $data = read_file($template);
    }

		// replace custom elements
		$data = str_replace( "{action}", $this->action, $data);
		$data = str_replace( "{updateScript}", $this->updateScript, $data);
		$data = str_replace( "{method}", $this->method, $data);
		$data = str_replace( "{returnUrl}", $this->returnUrl, $data);
		$_recinfo = "&nbsp;&nbsp;aangemaakt <b>".dbdatum($this->object->get("add_date"))." (".$this->object->get("add_user").")</b>, laatste mutatie <b>".dbdatum($this->object->get("change_date"))." (".$this->object->get("change_user").")</b>";
		$data = str_replace( "{recordInfo}", $_recinfo, $data);

    
		// replace data elements
    $templateObjects=array($this->object);
    foreach($this->objects as $object)
      $templateObjects[]=$object;

    foreach($templateObjects as $objectIndex=>$object)
		{
		  //echo "objectIndex $objectIndex <br>\n"; listarray($object->data['fields']);
	  	foreach($object->data['fields'] as $name=>$field)  
      {
			$input = $this->makeInput($name,$object);
    	$data = str_replace( "{".$name."_inputfield}", $input, $data);
      $templateVelden=array('description','value','error');
	  	//while ( list( $key, $val ) = each( $field ) )
      foreach($templateVelden as $key)
	  	{
	  	  $val=$field[$key];
	    	if(is_string($val))
	    	{

	    	  if ($key == "description")
          {
            if ( ! isset ($object->eigenVelden) || ! isset ($object->eigenVelden[$name]) ) {
              $val = vt($val);
            }

	    	    $data = str_replace( "{".$name."_".$key."}", "<label for=\"".$name."\" title=\"".$val."\">".$val."</label>", $data);
          }
          elseif ( $key === 'error' )
          {
            if(isset($this->object->data['fields'][$name]['noHtmlspecialchars']) && $this->object->data['fields'][$name]['noHtmlspecialchars']==true)
              $data = str_replace( "{".$name."_".$key."}", '<span class="help-block">' . $val . '</span>', $data);
            else
              $data = str_replace( "{".$name."_".$key."}", '<span class="help-block">' . htmlspecialchars($val) . '</span>', $data);
          }
	    	  else
          {
            if(isset($this->object->data['fields'][$name]['noHtmlspecialchars']) && $this->object->data['fields'][$name]['noHtmlspecialchars']==true)
              $data = str_replace( "{".$name."_".$key."}", $val, $data);
            else
              $data = str_replace( "{".$name."_".$key."}", htmlspecialchars($val), $data);
          }
	    		  
	    	}
	  	}
 		  }
    }
    
    		// extra formvars

    $data = $this->vtTags($data);

		reset($this->formVars);

		while ( list( $key, $val ) = each( $this->formVars ) )
		{
			$data = str_replace( "{".$key."}", $val, $data);
		}

 		if(!isset($this->skipStripAll))
	    $data = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $data);
	  else
	    $data = eregi_replace( "\{[a-zA-Z0-9_-]+_error\}", "", $data);
	  return $data;
	}

	function vtTags($data)
  {
    preg_match_all('/\[vt\](.*?)\[\/vt\]/s', $data, $matches);
    if (is_array($matches))
    {
      foreach ($matches[1] as $item)
      {
        $vtText = vt($item);
        $data = str_replace("[vt]{$item}[/vt]", $vtText, $data);
      }
    }
    return $data;
  }

  function htmlList ($fieldName, $values, $options)
  {
    $attributePairs = array();
    foreach ($options as $key => $val) {
      if (is_int($key)) {
        $attributePairs[] = $val;
      } else {
        $val = htmlspecialchars($val, ENT_QUOTES);
        $attributePairs[] = $key . ' = "' . $val . '"';
      }
    }

    $selectOptions = "\t<option value='-'>---</option>\n";
    foreach ($values as $key => $value) {
      $selectOptions .= sprintf("\t<option value='%s'>%s</option>\n", $key, $value);
    }
    return '<select name="' . $fieldName . '" id="' . $fieldName . '" ' . join(' ', $attributePairs) . '>' . $selectOptions . '</select>';
  }
}