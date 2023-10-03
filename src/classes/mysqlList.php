<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/18 15:12:52 $
 		File Versie					: $Revision: 1.134 $
*/

class MysqlList2
{

	var $BTR_REMOVE_QUERY_LIMIT;

  var $tables;
  var $columns;
  var $objects;

  /* Query stuff */
  var $queryWhere;
  var $querySelect;
  var $queryOrder;
  var $queryDirection;
  var $queryJoin;
  var $queryGroupBy;
  var $searchString;
  var $searchFields;
  var $filterString;
  var $queryString;
  var $page;
  var $perPage;
  var $editScript;
  var $customEdit;
  var $DB;
  var $dbId;
  var $xlsFilename;
  var $extraFormHeaderTags;
  var $sortOptions;
  var $filterHeaderOptions = array();

  function MysqlList2()
  {
		global $__BTR_CONFIG;

		$this->BTR_REMOVE_QUERY_LIMIT            = $__BTR_CONFIG["REMOVE_QUERY_LIMIT"];

    // init stuff
    $this->tables         = array();
    $this->columns        = array();
    $this->objects        = array();
    $this->queryOrder     = array();
    $this->queryDirection = array();
    $this->searchFields   = array();
    $this->searchString   = "";
    $this->sqlQuery       = "";
    $this->queryString    = $_GET;
    $this->perPage        = 25;
    $this->RowCounter     = 0;
    $this->Records        = 0;
    $this->page           = 1;
    $this->dbId           = 1;
    $this->pixelsPerChar  = 6;
    //$this->pixelsPerChar  = 9;
    $this->idField        = 'id';
    $this->idTable        = '';
    $this->fixedFields    = array();
    $this->setFilters     = array();
    $this->DB = new DB($this->dbId);
    $this->noExport       = false;
    $this->editIconTd     ='';
    $this->editIconExtra  ='';
    $this->xlsFilename    ='';
    $this->noGroup        = true;
    $this->hideFilter     = false;
  }




  function getCustomFields($objectNamen,$configName='',$customFields='',$extraProfiel='')
  {
    $cfg=new AE_config();
    global $USR;
    $toolTips = array();
    if(!is_array($objectNamen))
    {
      $tmp = $objectNamen;
      $objectNamen=array();
      $objectNamen[] = $tmp;
    }

    $aantalObjecten=count($objectNamen);

    if($configName == '')
      $customSessionField=$objectNamen[0];
    else
      $customSessionField = $configName;


    if(isset($_POST['profiel']))
      $_SESSION['tableSettings']['profiel']=$_POST['profiel'];
    elseif ($_SESSION['tableSettings']['table'] != $customSessionField)
      $_SESSION['tableSettings']['profiel']='';

    $_SESSION['tableSettings']['table']=$customSessionField;

    if($extraProfiel<>'' && $_SESSION['tableSettings']['profiel']=='')
      $customSessionField=$customSessionField.$extraProfiel;
    else
      $customSessionField=$customSessionField.$_SESSION['tableSettings']['profiel'];

    if(!isset($_SESSION[$customSessionField]))
    {
      $cfg=new AE_config();
      $_SESSION[$customSessionField]=unserialize($cfg->getData($USR.'_'.$customSessionField));
    }

    $_SESSION['tableSettings']['currentTable']=$customSessionField;

    $fieldsToShow=array();
    foreach ($objectNamen as $objectNaam)
    {
      $fields=array();
      $object = new $objectNaam();
      if(is_array($customFields[$objectNaam]))
      {
        $object->data['fields']=$customFields[$objectNaam];
      }

      $fixedFields=array();
      foreach ($this->fixedFields as $dataArray)
      {
        $fixedFields[$dataArray['objectname']][]=$dataArray['table'].".".$dataArray['name'];
      }

      if(isset($_POST['veldVolgorde']) && is_array($_POST['veldVolgorde']))
        $_SESSION[$customSessionField]['veldVolgorde']=$_POST['veldVolgorde'];


      if(is_array($fixedFields[$objectNaam]))
      {
        $_SESSION[$customSessionField]['fixedFields']=array();
        foreach ($fixedFields[$objectNaam] as $key)
          $_SESSION[$customSessionField]['fixedFields'][$key]=1;
      }
      else
      {
        foreach ($fixedFields as $key)
          $_SESSION[$customSessionField]['fixedFields'][$key]=1;
      }

      foreach ($object->data['fields'] as $name=>$eigenschappen)
      {
        if ( ! isset ($object->eigenVelden) || ! isset ($object->eigenVelden[$name]) ) {
          $eigenschappen["description"] = ($eigenschappen["description"]); // call 6055 appVertaling
        }

        $toolTips[$name] = ($eigenschappen["description"]);
        if($eigenschappen['list_visible'] == true || $_SESSION[$customSessionField]['fixedFields'][$object->data['table'].".".$name] == 1)
        {
          if(!$eigenschappen['categorie'])
          {
            $eigenschappen['categorie'] = ("Algemeen");
          }
          $fields[$eigenschappen['categorie']][$object->data['table'].".".$name] = substr(vt($eigenschappen['description'], true, false),0,15);
          $veldOmschrijving[$objectNaam.".".$name] = substr(($eigenschappen['description']),0,15);
        }
      }

      ksort($fields);

      foreach ($_POST as $key=>$value)
      {
        $fixedPost[str_replace("@",".",$key)]=$value;
      }

      $postNames = array_keys($fixedPost);
      if($_POST['kolUpdate'] > 0)
      {
        foreach ($fields as $categorie=>$velden)
        {
          foreach ($velden as $key=>$value)
          {
            if(in_array($key,$postNames))
              $_SESSION[$customSessionField]['fields'][$key]=1;
            else
              $_SESSION[$customSessionField]['fields'][$key]=0;
          }
        }
      }
      // Filter settings
      //

      foreach ($_POST as $key=>$value)
      {
        if(substr($key,0,6)=='filter')
        {
          $splitKey=explode('_',substr($key,7));
          $this->filterOptions[$splitKey[0]][$splitKey[1]]=$value;
        }

        if(substr($key,0,4)=='sort')
        {
          $splitKey=explode('_',substr($key,5));
          $this->sortOptions[$splitKey[0]][$splitKey[1]]=$value;
        }
        if(substr($key,0,5)=='group')
        {
          $splitKey=explode('_',substr($key,6));
          $this->groupOptions[$splitKey[0]][$splitKey[1]]=$value;
        }
      }

      foreach ($this->filterOptions as $id=>$waarden)
        if($waarden['verwijder'] == 1)
          unset($this->filterOptions[$id]);

      foreach ($this->sortOptions as $id=>$waarden)
        if($waarden['verwijder'] == 1)
          unset($this->sortOptions[$id]);

      foreach ($this->groupOptions as $id=>$waarden)
        if($waarden['verwijder'] == 1)
          unset($this->groupOptions[$id]);

      if($_POST['addFilter'] <> '')
      {
        $veldnaam= explode('.',$_POST['addFilter']);
        // listarray($veldnaam); listarray($object);
        if(is_array($object->data['fields'][$veldnaam[1]]))
        {
          if(!in_array($_POST['addFilter'],$this->setFilters))
          {
            $this->filterOptions[]=array('veldnaam'=>$_POST['addFilter'],'methode'=>'gelijk');
            $this->setFilters[]=$_POST['addFilter'];
          }
        }
        elseif (is_array($object->data['alias'][$veldnaam[1]]))
        {
          $this->filterOptions[]=array('veldnaam'=>$object->data['alias'][$veldnaam[1]]['sql_alias'],'methode'=>'gelijk');
          $this->setFilters[]=$_POST['addFilter'];
        }
      }
      if($_POST['addSort'] <> '')
      {
        foreach ($this->sortOptions as $waarden)
        {
          $veldnamen[]=$waarden['veldnaam'];
        }

        if(!in_array($_POST['addSort'],$veldnamen))
          $this->sortOptions[]=array('veldnaam'=>$_POST['addSort'],'methode'=>'ASC');
        $_POST['addSort']='';
      }


      if($_POST['addGroup'] <> '')
      {
        foreach ($this->sortOptions as $waarden)
        {
          $veldnamen[]=$waarden['veldnaam'];
        }

        if(!in_array($_POST['addGroup'],$veldnamen))
          $this->groupOptions[]=array('veldnaam'=>$_POST['addGroup']);
        $_POST['addGroup']='';
      }

      if(is_array($this->filterOptions))
        $_SESSION[$customSessionField]['filter']=$this->filterOptions;
      if(is_array($this->sortOptions))
        $_SESSION[$customSessionField]['sort']=$this->sortOptions;
      if(is_array($this->groupOptions))
        $_SESSION[$customSessionField]['group']=$this->groupOptions;
      // end filter settinfs
      //
      if($_POST['resetFilter'] == 1)
      {
        $_SESSION[$customSessionField]['filter']=array();
        $_SESSION[$customSessionField]['sort']=array();
      }

      if($_POST['kolUpdate'] == 2)
      {
        $cfg->addItem($USR.'_'.$customSessionField,serialize($_SESSION[$customSessionField]));
      }
      elseif ($_POST['kolUpdate'] == 3)
      {
        $tmpvar=unserialize($cfg->getData($USR.'_'.$customSessionField));
        if($tmpvar != "")
          $_SESSION[$customSessionField]=$tmpvar;
      }

      $this->filterOptions=$_SESSION[$customSessionField]['filter'];
      $this->sortOptions=$_SESSION[$customSessionField]['sort'];
      $this->groupOptions=$_SESSION[$customSessionField]['group'];

      if(!is_array($this->categorieVolgorde) || $noCategorieVolgorde == true)
      {
        $noCategorieVolgorde = true;
        $this->categorieVolgorde=array($objectNaam => array(('Algemeen')));
      }
      elseif(!is_array($this->categorieVolgorde[$objectNaam]))
      {
        $this->categorieVolgorde[$objectNaam] = $this->categorieVolgorde;
      }

      if($aantalObjecten > 1)
      {

        $objTit = (isset($object->omschrijving))?vt($object->omschrijving):vt($objectNaam);
        $html_opties .= "<b>$objTit</b>";

      }

      foreach ($this->categorieVolgorde[$objectNaam] as $id=>$categorie)
      {

        $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$objectNaam$id')\">".vt($categorie)."</div><span class=\"submenu\" id=\"sub$objectNaam$id\">\n";

        foreach ($fields[$categorie] as $key=>$value)
        {

          $fieldNameArray= explode('.',$key);
          $disabled = "";
          $checked = "";
          if($_SESSION[$customSessionField]['fixedFields'][$key] > 0)
          {
            $disabled = "disabled";
            $checked = "checked";
            $fieldOptions = array();
            foreach ($this->fixedFields as $options)
            {
              if($objectNaam == $options['objectname'] && $fieldNameArray[1] == $options['name'])
                $fieldOptions= $options['options'];
            }
            //$this->addField($objectNaam,$fieldNameArray[1],$fieldOptions);
            $fieldsToShow[$objectNaam.'.'.$fieldNameArray[1]]=$fieldOptions;

          }
          elseif($_SESSION[$customSessionField]['fields'][$key] > 0)
          {
            //$this->addField($objectNaam,$fieldNameArray[1],array('search'=>$object->data['fields'][$fieldNameArray[1]]['list_search']));
            $fieldsToShow[$objectNaam.'.'.$fieldNameArray[1]]=array('search'=>$object->data['fields'][$fieldNameArray[1]]['list_search']);
            $checked = "checked";
          }

          $atKey = str_replace('.',"@",$key);
          $varSplit = explode(".", $key);
          $balloon = $toolTips[$varSplit[1]];
          //$html_opties .= "<label for=\"".$key."\" title=\"".$Naw->data['fields'][$key]['description']."\"><input type=\"checkbox\" value=\"1\" id=\"".$key."\" name=\"$atKey\" $checked $disabled > ".$value." </label><br>\n";
          $html_opties .= "
        <span class='toolTip'  title='" . vt($balloon) . " \n(" . $varSplit[1] . ")'>
	      <label for='$key' >
	        <input type='checkbox' value='1' id='$key' name='$atKey' sortkey='$objectNaam.$varSplit[1]' $checked $disabled > $value
	      </label></span><br>
	      ";
        }
        $html_opties .= "</span>\n";
      }
    }

    $volgordeOptions='';
    if(is_array($_SESSION[$customSessionField]['veldVolgorde']))
    {
      foreach ($_SESSION[$customSessionField]['veldVolgorde'] as $veld)
      {
        $parts=explode('.',$veld);
        if(isset($fieldsToShow[$veld]))
        {
          $this->addField($parts[0],$parts[1],$fieldsToShow[$veld]);
          $addedFields[]=$veld;
          $volgordeOptions.='<option value="'.$veld.'" >'.$veldOmschrijving[$veld]."</option>\n";
        }
      }
    }

    foreach ($fieldsToShow as $field=>$options)
    {
      if(!in_array($field,$addedFields))
      {
        $parts=explode('.',$field);
        $this->addField($parts[0],$parts[1],$options);
        $volgordeOptions.='<option value="'.$field.'" >'.$veldOmschrijving[$field]."</option>\n";
      }

    }


    $namesVeld=$_SESSION['tableSettings']['table']."_n_$USR";
    $names=unserialize($cfg->getData($namesVeld));
    $profiel=array();
    for($i=1;$i<21;$i++)
    {
      if($_SESSION['tableSettings']['profiel'] == $i )
        $selected='selected';
      else
        $selected='';

      if(isset($names[$i]) && $names[$i] <> '')
        $naam=$names[$i];
      else
        $naam="profiel $i";

      $profielSelect .= "<option value=\"$i\" ".$selected.">$naam</option>\n";
    }

    $refresh=maakKnop('refresh.png',array('size'=>16,'text'=>vt('Aanpassen'),'tooltip'=>vt('Veldselectie aanpassen')));
    $instellingen=maakKnop('gear.png',array('size'=>16,'text'=>vt('Instellingen'),'tooltip'=>vt('Instellingen')));

    $html = "
 <div id=debug> </div>
 <script type=\"text/javascript\" src=\"javascript/jquery-min.js\"></script>
 <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>
 <script language=\"JavaScript\" TYPE=\"text/javascript\">




function hernoemProfiel()
{
  var val=$(\"#profiel\").val();
  var oudeNaam=$(\"#profiel option:selected\").text();
  var nieuweNaam=prompt(\"".vt("Hernoemen van profiel")."\",oudeNaam);
  if (nieuweNaam!=null && nieuweNaam!=\"\")
  {
    $.ajax({url: \"lookups/ajaxSetProfileName.php?profile=\"+val+\"&veld=".$namesVeld."&name=\"+nieuweNaam}).done(function() {
     $('#profiel option[value=\"' + val + '\"]').text(nieuweNaam);
    });
  }
}

 	function selectSelected()
	{
	  if(document.kolForm['veldVolgorde[]'])
	  {
	  	var veldVolgorde 	= document.kolForm['veldVolgorde[]'];
    	for(j=0; j < veldVolgorde.options.length; j++)
		  {
 		  	veldVolgorde.options[j].selected = true;
		  }
	  }
  }

function Aanpassen()
{
  selectSelected();
	document.kolForm.submit();
}
function Opslaan()
{
	document.kolForm.kolUpdate.value=\"2\";
	selectSelected();
	document.kolForm.submit();
}
function Herladen()
{
	document.kolForm.kolUpdate.value=\"3\";
	document.kolForm.submit();
}
</script>

<form name=\"kolForm\" target=\"content\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\" >
<br><br><b>".vt("Selecties")."</b>



<div class=\"menutitle\" onclick=\"javascript:Aanpassen();\">
 $refresh
</div>
<br>


<div class=\"menutitle\" onclick=\"SwitchMenu('subNav')\">
 $instellingen
</div>


<span class=\"submenu\" id=\"subNav\">
<input type=\"hidden\" name=\"kolUpdate\" value=\"1\">
<br>
<table><tr><td>
<div onclick=\"javascript:hernoemProfiel();\" style=\"width:13px\"><img src=\"icon/16/gear.png\"> </img></div>
</td><td>
<select class=\"\" type=\"select\"  name=\"profiel\"  id=\"profiel\" >
<option value=\"\">standaard</option>
".$profielSelect."
</select>
</td></tr></table>
<br><br>

<input type=\"button\" onclick=\"parent.frames['content'].showMysqlListConfig();\"   value=\"".vt("Importeren")."\" style=\"width:100px\"><br><br>
<input type=\"button\" onclick=\"javascript:Herladen();\"   value=\"".vt("Laden")."\" style=\"width:100px\"><br><br>
<input type=\"button\" onclick=\"javascript:Opslaan();\"   value=\"".vt("Opslaan")."\" style=\"width:100px\"><br><br>

<table>
  <td>
	  <select name=\"veldVolgorde[]\" multiple size=\"10\" style=\"width : 100px\">
    ".$volgordeOptions."
	  </select>
  </td>
  <td width=\"40\" >
	  <a style=\"width: 20px;\" href=\"javascript:moveOptionUp(document.kolForm['veldVolgorde[]'])\">
		  <img src=\"images/16/pijl_omhoog.png\"  border=\"0\" alt=\"omhoog\" align=\"absmiddle\">
	  </a>
	  <br><br>
	  <a style=\"width: 20px;\" href=\"javascript:moveOptionDown(document.kolForm['veldVolgorde[]'])\">
		  <img src=\"images/16/pijl_omlaag.png\"  border=\"0\" alt=\"omlaag\" align=\"absmiddle\">
	  </a>
  </td>
</table>

</span>
</div>

<style type=\"text/css\">
.menutitle{
cursor:pointer;
margin-bottom: 5px;
background-color:#ECECFF;
color:#000000;
width:120px;
padding:2px;
text-align:center;
font-weight:bold;
border:1px solid #000000;
}

input {
	color: Navy;
	background-color:#FBFBFB;
	font-size:14px;
	border : 0px;
	border-bottom : 1px solid silver;
	border-left : 1px solid silver;
	font-weight: bold;
}

.submenu{
margin-bottom: 0.5em;
display: none;
}
</style>

<script type=\"text/javascript\" src=\"javascript/menu.js\"></script>

<div id=\"masterdiv\">
";
    $html .= $html_opties;
    $html .="</div>";
    $html .="</form>";
    return $html;

  }

  // depricated function
  function addField($objectName, $column, $options=array())
  {
    $this->addColumn($objectName, $column, $options);
  }
  /*
    function addFixedField($objectName, $column, $options=array())
    {
      if(!$this->object[$objectName] && class_exists($objectName))
       $this->object[$objectName] = new $objectName;
      $tmp['name'] 		 	= $column;
      $tmp['table'] 		 = $this->object[$objectName]->data[table];
      $tmp['objectname'] = $objectName;
      $options['table'] 		 = $this->object[$objectName]->data['table'];
      $tmp['options'] 	 = $options;
      array_push($this->fixedFields,$tmp);
    }
    */
  function addFixedField($objectName, $column, $options=array())
  {
    if(!$this->object[$objectName] && class_exists($objectName))
      $this->object[$objectName] = new $objectName;

    $tmp['name'] 		 	= $column;
    if($objectName=='')
    {
      $tmp['table'] 		 = '';
      $tmp['objectname'] = 'geen';
      $options['customField']=True;
      $tmp['options'] 	 = $options;
    }
    else
    {
      $tmp['table'] 		 = $this->object[$objectName]->data['table'];
      $tmp['objectname'] = $objectName;
      $options['table'] 		 = $tmp['table'] ;
      $tmp['options'] 	 = $options;
    }
    array_push($this->fixedFields,$tmp);
  }

  function addColumn($objectName, $column, $options=array())
  {
    $tmp['name'] 		 	= $column;
    //$tmp['table'] 		 = $this->object[$objectName]->data[table]; // hier word niks mee gedaan?
    $tmp['objectname'] = $objectName;
    //if(!is_object($this->object[$objectName]))
    //$this->object[$objectName] = new $objectName();
    $options['table'] 		 = $this->object[$objectName]->data['table'];
    $tmp['options'] 	 = $options;
    array_push($this->columns,$tmp);
  }

  function removeColumn($column)
  {
    $tmpArray = array();
    for($x=0; $x < count($this->columns);$x++)
    {
      if ($this->columns[$x][name] <> "$column")
      {
        $tmpArray[] = $this->columns[$x];
      }

    }
    $this->columns = $tmpArray;
  }

  function setSelect($txt)
  {
    $this->querySelect = $txt;
  }

  function setWhere($where)
  {
    $this->queryWhere = $where;
  }

  function setFilter()
  {
    foreach ($_POST as $key=>$value)
    {
      if(substr($key,0,6)=='filter')
      {
        $splitKey=explode('_',substr($key,7));
        $this->filterOptions[$splitKey[0]][$splitKey[1]]=$value;
      }

      if(substr($key,0,4)=='sort')
      {
        $splitKey=explode('_',substr($key,5));
        $this->sortOptions[$splitKey[0]][$splitKey[1]]=$value;
      }

      if(substr($key,0,5)=='group')
      {
        $splitKey=explode('_',substr($key,5));
        $this->sortOptions[$splitKey[0]][$splitKey[1]]=$value;
      }
    }

    foreach ($this->filterOptions as $id=>$waarden)
      if($waarden['verwijder'] == 1)
        unset($this->filterOptions[$id]);

    foreach ($this->sortOptions as $id=>$waarden)
      if($waarden['verwijder'] == 1)
        unset($this->sortOptions[$id]);

    if($_POST['addFilter'] <> '')
      $this->filterOptions[]=array('veldnaam'=>$_POST['addFilter'],'methode'=>'gelijk');
    if($_POST['addSort'] <> '')
    {
      foreach ($this->sortOptions as $waarden)
      {
        $veldnamen[]=$waarden['veldnaam'];
      }

      if(!in_array($_POST['addSort'],$veldnamen))
        $this->sortOptions[]=array('veldnaam'=>$_POST['addSort'],'methode'=>'ASC');
    }

    if($_POST['addGroup'] <> '')
    {
      $veldnamen=array();
      foreach ($this->groupOptions as $waarden)
      {
        $veldnamen[]=$waarden['veldnaam'];
      }

      if(!in_array($_POST['addGroup'],$veldnamen))
        $this->groupOptions[]=array('veldnaam'=>$_POST['addGroup']);
    }
  }

  function addSort($veldnaam, $methode="ASC")
  {
    $this->sortOptions[] = array(
      'veldnaam'     => $veldnaam,
      'methode'      => $methode,
      'uitschakelen' => false
    );
  }

  function setSearch($search)
  {
    $this->searchString = $search;
  }

  function setOrder($sort,$direction)
  {
    for($a=0;$a < count($sort); $a++)
    {
      $this->queryOrder[$a+1] = $sort[$a];
      $this->queryDirection[$a+1] = $direction[$a];
    }
  }

  function setJoin($join)
  {
    $this->queryJoin = $join;
  }

  function setGroupBy($groupBy)
  {
    $this->queryGroupBy = $groupBy;
  }

  function getSQL()
  {
    // build table select
    for($a=0;$a < count($this->columns); $a++)
    {
      if(!$this->objects[$this->columns[$a]['objectname']] && $this->columns[$a]['objectname'] != "")
      {
        $objectName = $this->columns[$a]['objectname'];
        if(class_exists($objectName))
          $this->objects[$this->columns[$a]['objectname']] =  new $objectName();
        $this->objects[$this->columns[$a]['objectname']]->data['fullId']=$this->objects[$this->columns[$a]['objectname']]->data['table'].".".$this->objects[$this->columns[$a]['objectname']]->data['identity'];
      }
      $selectionId[] = $this->objects[$this->columns[$a]['objectname']]->data['fullId']." ";



      if($this->columns[$a]['objectname'] != "")
      {
        // dit is een database veld, maak een query selectie item (en add table)
        $selection[$this->objects[$this->columns[$a]['objectname']]->data['table'].".".$this->columns[$a]['name']] = $this->objects[$this->columns[$a]['objectname']]->data['table'].".".$this->columns[$a]['name'];
        $tables[] = $this->objects[$this->columns[$a]['objectname']]->data['table'];
      }
      else
      {
        if($this->columns[$a]['options']['sql_alias'])
        {
          // maak een database alias aan
          $selection[".".$this->columns[$a]['name']] = $this->columns[$a]['options']['sql_alias']." ";//AS ".$this->columns[$a][name];
        }
        else
        {
          // dit is geen database veld zet hem niet in de selectie.
          //$selection[] = $this->columns[$a][name];
        }
      }
    }
    $tables = array_unique($tables);

    if($this->ownTables)
      $tables = $this->ownTables;

    foreach ($this->groupOptions as $veldId=>$waarden)
    {
      $veldnaam=$waarden['veldnaam'];
      if($waarden['uitschakelen']==false)
        $group[] = $veldnaam;
      $this->group=true;
    }

    if($this->idTable != '')
      $query  = "SELECT ".$this->idTable.".".$this->idField." as ".$this->idField." ";
    else
      $query  = "SELECT ".$selectionId[0]." as ".$this->idField." ";

    foreach ($selection as $key=>$field)
    {
      $query .= ", $field as `$key` " ;
    }

    if($this->group)
      $query .= ", count(*) as `aantalRecords` " ;

    $query .= " FROM (".implode(", ",$tables).") ";

    if($this->queryJoin)
      $query .= " ".$this->queryJoin." ";

    $query .= " WHERE 1 ";



    if(is_array($this->filterOptions))
    {
      $methods=array("gelijk"=>"=","nietGelijk"=>"<>","groter"=>">","kleiner"=>"<","groterGelijk"=>">=",'kleinerGelijk'=>"<=");


      foreach ($this->columns as $col)
      {
        $veldTypen[$this->objects[$col['objectname']]->data['table'].".".$col['name']]=$this->objects[$col['objectname']]->data['fields'][$col['name']]['form_type'];
      }

      foreach ($this->filterOptions as $veldId=>$waarden)
      {
        $waarden['waarde']=mysql_escape_string($waarden['waarde']);
        $veldnaam=$waarden['veldnaam'];
        if($veldTypen[$veldnaam] == 'calendar')
        {
          $tmp=explode("-",$waarden['waarde']);
          $tmp=array_reverse($tmp);
          $waarden['waarde']=implode("-",$tmp);
        }
        if($waarden['uitschakelen'] == true)
        {
          //skip
        }
        elseif($methods[$waarden['methode']])
        {
          $query .= " AND $veldnaam ".$methods[$waarden['methode']]." '".$waarden['waarde']."'";
        }
        else
        {
          switch ($waarden['methode'])
          {
            case 'bevat' :
              $query .= " AND $veldnaam like '%".$waarden['waarde']."%'";
              break;
            case 'bevatniet' :
              $query .= " AND $veldnaam NOT like '%".$waarden['waarde']."%'";
              break;
            case 'begintmet' :
              $query .= " AND $veldnaam like '".$waarden['waarde']."%'";
              break;
            case 'eindigtmet' :
              $query .= " AND $veldnaam like '%".$waarden['waarde']."'";
              break;
            case 'inlijst' :
              $query .= " AND $veldnaam IN('".str_replace(',',"','",$waarden['waarde'])."')";
              break;
            case 'isnull' :
              $query .= " AND $veldnaam is null";
              break;
            case 'isnotnull' :
              $query .= " AND $veldnaam is not null";
              break;
          }
        }
      }
    }

    if ($this->queryWhere)
    {
      $query .= " AND ".$this->queryWhere." ";
    }

    // set Where
    if($this->searchString)
    {
      for($a=0; $a < count($this->columns); $a++)
      {
        if($this->columns[$a]['options']['search'] == true)
        {
          if($this->columns[$a]['options']['sql_alias'])
          {
            $fieldName = $this->columns[$a]['options']['sql_alias'];
          }
          else
          {
            $fieldName = $this->objects[$this->columns[$a]['objectname']]->data['table'].".".$this->columns[$a]['name'];
          }

          $search[] = $fieldName." LIKE '%".mysql_escape_string($this->searchString)."%'";
        }
      }
      if(is_array($search))
      {
        $searchString = implode(" OR ",$search);
        $query .= " AND (".$searchString.") ";
      }
    }



    if(is_array($group) && count($group) > 0 && $group[0] <> '')
    {
      $query .= " GROUP BY ".implode(", ",$group);
    }
    else
    {
      // group by
      if($this->queryGroupBy)
      {
        $query .= " GROUP BY ".$this->queryGroupBy;
      }
    }

    foreach ($this->sortOptions as $veldId=>$waarden)
    {
      $veldnaam=$waarden['veldnaam'];
      if($waarden['methode'] && $waarden['uitschakelen']==false)
        $order[] = $veldnaam." ".$waarden['methode'];

    }

    if(is_array($order))
      $query .= " ORDER BY ".implode(", ",$order);


    //	$query.=" LIMIT 500";

    // limit perPage
    //$this->perPage=100;
		$from = $this->BTR_REMOVE_QUERY_LIMIT ? 0 : ($this->page-1) * ($this->perPage);
    $this->from = $from;
    if($this->storeTableIds <> '')
    {
      $DB=new DB($this->dbId);
      $queryparts=explode('FROM',$query);
      $newQuery='SELECT '.$this->storeTableIds.'.id as id FROM '.$queryparts[1];
      $_SESSION['lastTableIds']=array();
      $DB->SQL($newQuery);
      $DB->Query();
      while($data=$DB->nextRecord())
      {
        $_SESSION['lastTableIds'][]=$data['id'];
      }
    }


    $query .= " LIMIT ".$from.",". ($this->BTR_REMOVE_QUERY_LIMIT ? 100000 : $this->perPage);
    $this->sqlQuery = $query;
    //echo $query."<br>\n";//exit;
    return $query;
  }

  function setFullEditScript($script='')
  {
    $this->fullEditScript=$script;
  }

  function selectPage($page)
  {

    if($_POST['toXls'] == '1')
    {
      $this->setXLS($mainHeader.' '.$subHeader);
      $this->getXLS();
    }
    if(empty($page))
      $page = 1;
    elseif($page > ceil($this->records()/$this->perPage))
      $page = 1;

    $_GET['page']=$page;

    $this->page = $page;
    $this->DB = new DB($this->dbId);
    $this->DB->SQL($this->getSQL());
    return $this->DB->Query();
  }

  function records()
  {

    if($query = $this->getSQL())
    {

      if (!$this->DB->resultReady())
      {
        $this->DB = new DB($this->dbId);
        $this->DB->executeQuery($query);
      }

      $from = strpos($this->sqlQuery,"FROM");
      $tot  = strpos($this->sqlQuery,"LIMIT")-$from;

      $recordQuery = "SELECT count(*) as aantal ".substr($this->sqlQuery,$from,$tot);

      $db     = new DB($this->dbId);
      $db->SQL($recordQuery);
      $db->Query();

      $this->Records = 0;
      if($db->records() > 1)
      {
        $aantal['aantal'] = 0;
        while($rowCount = $db->nextRecord()) {
          $aantal['aantal'] += $rowCount['aantal'];
        }
        $this->Records = $aantal['aantal'];
      }
      elseif ($db->records() === 1)
      {
        $aantal = $db->nextRecord();
        $this->Records = $aantal['aantal'];
      }
      return $this->Records;
    }
    return 0;
  }

  //  returns fetcharray
  function getRow()
  {
    $this->RowCounter++;

	  if( $this->RowCounter > ($this->BTR_REMOVE_QUERY_LIMIT ? 100000 : $this->perPage))
      return false;

    if($result = $this->DB->nextRecord())
    {

      $data[$this->idField]['value']=$result[$this->idField];
      for($b=0;$b < count($this->columns); $b++)
      {
        $column = $this->columns[$b];

        if(!$this->objects[$column['objectname']] && $column['objectname'] != "")
        {
          $this->objects[$column['objectname']] =  new $column['objectname']();
        }
        $table=	$this->objects[$column['objectname']]->data['table'];
        // haal default options uit object.
        if($column['objectname'] != "")
        {
          $options = $this->objects[$column['objectname']]->data['fields'][$column['name']];
        }

        $data[$table.".".$column['name']] = $column['options'];
        $data[$table.".".$column['name']]['field'] = $column['name'];
        $data[$table.".".$column['name']]['value'] = $result[$table.".".$column['name']];

        if($column['objectname'] != "")
        {
          $data[$table.".".$column['name']] = array_merge($options,$data[$table.".".$column['name']]);
        }
      }
      if($this->group==true)
        $data['aantalRecords']['value']=$result['aantalRecords'];

      return $data;
    }
    else
      return false;
  }

  function filterHeader($addFilter)
  {
    $kal = new DHTML_Calendar();
    $AEmysqlListConfig = new AE_cls_mysqlListConfig();
    $output = $AEmysqlListConfig->getConfigHtml();
    $output.=$kal->get_load_files_code();

    $output.="<form name=\"editForm\" method=\"POST\"  ".$this->extraFormHeaderTags.">
	  <input type=\"hidden\" name=\"addSort\" value=\"\" >
    <input type=\"hidden\" name=\"addFilter\" value=\"\" >
    <input type=\"hidden\" name=\"addGroup\" value=\"\" >
    <input type=\"hidden\" name=\"toXls\" value=\"\" >
    <input type=\"hidden\" name=\"resetFilter\" value=\"\" > ";

    foreach ($this->columns as $col)
    {

      $omschrijving[$this->objects[$col['objectname']]->data['table'].".".$col['name']]=$this->objects[$col['objectname']]->data['fields'][$col['name']]['description'];
      $veldTypen[$this->objects[$col['objectname']]->data['table'].".".$col['name']]=$this->objects[$col['objectname']]->data['fields'][$col['name']]['form_type'];
      if($this->objects[$col['objectname']]->data['fields'][$col['name']]['form_type']=="select" && is_array($this->objects[$col['objectname']]->data['fields'][$col['name']]['form_options']))
      {
        $tmp=array();
        foreach($this->objects[$col['objectname']]->data['fields'][$col['name']]['form_options'] as $index=>$value)
          $tmp[$value]=$value;
        $options[$this->objects[$col['objectname']]->data['table'].".".$col['name']]=$tmp;
      }
      else
        $options[$this->objects[$col['objectname']]->data['table'].".".$col['name']]=$this->objects[$col['objectname']]->data['fields'][$col['name']]['form_options'];
    }

    if(is_array($this->objects[$col['objectname']]->data['alias']))
      foreach ($this->objects[$col['objectname']]->data['alias'] as $key=>$value)
        $omschrijving[$value['sql_alias']]=$key;

    $filterOutput='';
    if(is_array($this->filterOptions) && count($this->filterOptions) > 0)
    {
      foreach ($this->filterOptions as $field_id=>$filteroptions)
      {
        if($filteroptions['hidden']==true)
          continue;
        $uitchakelen=($filteroptions['uitschakelen'] =="1")?"checked":'';
        if($veldTypen[$filteroptions['veldnaam']] == 'calendar')
        {
          $inp = array ('name' =>"filter_".$field_id."_waarde",'value' =>$filteroptions['waarde'],'size'  => "17");
          $inputField = $kal->make_input_field("",$inp,'');
        }
        else
        {
          $pulldown=false;

          if($filteroptions['methode'] == 'isnull')
          {
            $pulldown=true;
            $values[]='null';
          }
          if($filteroptions['methode'] == 'isnotnull')
          {
            $pulldown=true;
            $values[]='not null';
          }
          elseif($filteroptions['methode'] == 'gelijk')
          {
            if($veldTypen[$filteroptions['veldnaam']] && is_array($options[$filteroptions['veldnaam']]))
            {
              $pulldown=true;
              $values=$options[$filteroptions['veldnaam']];
            }
            else
            {
              $veldGegevens=explode(".",$filteroptions['veldnaam']);
              if(count($veldGegevens)==2)
              {
                $db=new DB($this->dbId);

                if ($this->filterHeaderOptions[$filteroptions['veldnaam']]["queryOverride"] != "")  // call 7630
                {
                  $query = $this->filterHeaderOptions[$filteroptions['veldnaam']]["queryOverride"];
                }
                else
                {
                  $query="SELECT ".$filteroptions['veldnaam']." FROM ".$veldGegevens[0]." GROUP BY ".$filteroptions['veldnaam']." ORDER BY ".$filteroptions['veldnaam'] . " LIMIT 0,160";
                }

                if($this->storeTableIds=='CRM_naw')
                {
                  $query="SELECT ".$filteroptions['veldnaam']." FROM ".$this->ownTables[0]." ".$this->queryJoin." WHERE  ".$this->queryWhere." GROUP BY ".$filteroptions['veldnaam']." ORDER BY ".$filteroptions['veldnaam'];
                }



                $db->SQL($query);
                $db->Query();
                if($db->records() < 150)
                {
                  $values=array();
                  $pulldown=true;
                  while($data = $db->nextRecord())
                    $values[$data[$veldGegevens[1]]]=$data[$veldGegevens[1]];
                }

              }
            }
          }
          if($pulldown==true)
          {
            $inputField="<select name=\"filter_".$field_id."_waarde\">\n";
            foreach ($values as $key=>$value)
            {
              if($key == $filteroptions['waarde'])
                $selected = "SELECTED";
              else
                $selected = "";
              $inputField.= "<option value=\"$key\"$selected>$value </option>\n";
            }
            $inputField .= "</select>";

          }
          else
            $inputField =  "<input type=\"text\" value=\"".$filteroptions['waarde']."\" name=\"filter_".$field_id."_waarde\">";
        }

        if(isset($omschrijving[$filteroptions['veldnaam']]))
          $veldOmschrijving=$omschrijving[$filteroptions['veldnaam']];
        else
          $veldOmschrijving=$filteroptions['veldnaam'];
        $veldOmschrijving = vt($veldOmschrijving);
        $filterOutput .= "<tr>
	        <td>  <input type=\"checkbox\" value=\"1\" name=\"filter_".$field_id."_uitschakelen\" ".$uitchakelen."> </td>
	        <td> ".$veldOmschrijving." <input type=\"hidden\" name=\"filter_".$field_id."_veldnaam\" value=\"".$filteroptions['veldnaam']."\" > &nbsp;&nbsp;&nbsp;</td>
	        <td> ".$this->createOptions($field_id,$filteroptions['methode'])." </td>
	        <td> $inputField </td>
	        <td>  <input type=\"checkbox\" value=\"1\" name=\"filter_".$field_id."_verwijder\"> </td>
	        <td> <input type=\"submit\" value=\"".vt("verwerk")."\" onclick=\"document.editForm.toXls.value='0'\";> </td>
	        </tr>";
      }
      if($filterOutput<>'')
        $output .= "
        <table>
          <tr>
            <td>".vt("uit")."</td>
            <td>".vt("veldnaam")."</td>
            <td>".vt("filter methode")."</td>
            <td>".vt("waarde")."</td>
            <td>".vt("verwijder")."</td>
          </tr>
          $filterOutput
        </table><br>";
    }

    if(is_array($this->sortOptions) && count($this->sortOptions) > 0 && $this->hideFilter==false)
    {
      $output .= "<table> ";
      $output .= "<tr><td>".vt("uit")."</td><td>".vt("veldnaam")."</td><td>".vt("sortering")."</td><td>".vt("verwijder")."</td></tr>";
      foreach ($this->sortOptions as $field_id=>$sortoptions)
      {
        $uitchakelen=($sortoptions['uitschakelen'] =="1")?"checked":'';
        $output .= "<tr>
	        <td>  <input type=\"checkbox\" value=\"1\" name=\"sort_".$field_id."_uitschakelen\" ".$uitchakelen."> </td>
	        <td> ".vt($omschrijving[$sortoptions['veldnaam']])." <input type=\"hidden\" name=\"sort_".$field_id."_veldnaam\" value=\"".$sortoptions['veldnaam']."\" > </td>
	        <td> ".$this->createOptions($field_id,$sortoptions['methode'],true)." </td>
	        <td>  <input type=\"checkbox\" value=\"1\" name=\"sort_".$field_id."_verwijder\"> </td>
	        <td> <input type=\"submit\" value=\"".vt("verwerk")."\"> </td>
	        </tr>";
      }
      $output .= "</table><br>";
    }

    if(is_array($this->groupOptions) && count($this->groupOptions) > 0 && $this->hideFilter==false)
    {
      $output .= "<table> ";
      $output .= "<tr><td>uit</td><td> veldnaam </td><td>verwijder</td></tr>";
      foreach ($this->groupOptions as $field_id=>$groupOptions)
      {
        $uitchakelen=($groupOptions['uitschakelen'] =="1")?"checked":'';
        $output .= "<tr>
	        <td>  <input type=\"checkbox\" value=\"1\" name=\"group_".$field_id."_uitschakelen\" ".$uitchakelen."> </td>
	        <td> ".$omschrijving[$groupOptions['veldnaam']]." <input type=\"hidden\" name=\"group_".$field_id."_veldnaam\" value=\"".$groupOptions['veldnaam']."\" > </td>
	        <td>  <input type=\"checkbox\" value=\"1\" name=\"group_".$field_id."_verwijder\"> </td>
	        <td> <input type=\"submit\" value=\"verwerk\"> </td>
	        </tr>";
      }
      $output .= "</table><br>";
    }

    if($_SESSION['usersession']['gebruiker']['CRMxlsExport']==0 && $this->noExport==false)
      $output .= "<a href=\"#\" onclick=\"document.editForm.toXls.value='1';document.editForm.submit();document.editForm.toXls.value='0'\" ><img alt=\"".vt("Naar Xls")."\" src=\"images/16/xls.gif\" width=\"16\" height=\"16\" border=\"0\"> ".vt("Naar Xls")." </a> &nbsp;&nbsp;";
    if($this->extraButtons)
      $output .= $this->extraButtons;
    if(($filterOutput<>'') || (is_array($this->sortOptions) && count($this->sortOptions) > 0 && $this->hideFilter==false))
      $output .= "<a href=\"#\" onclick=\"document.editForm.resetFilter.value='1';document.editForm.submit();document.editForm.resetFilter.value='0'\" ><img alt=\"Reset filter\" src=\"images/16/delete.gif\" width=\"16\" height=\"16\" border=\"0\"> ".vt("Filter verwijderen")." </a> <br>";

    if(!isset($this->skipCloseForm))
      $output.="</form>";

    return $output;
  }

  function createOptions($field_id,$selectedValue,$sort=false)
  {
    if($sort)
    {
      //$options = array('ASC'=>'oplopend','DESC'=>'aflopend');
      // $field_id = 'sort_'.$field_id;
      $html="<input type=\"hidden\" name=\"sort_".$field_id."_methode\" value=\"$selectedValue\">\n";
      if($selectedValue=='ASC')
        $disable=true;
      else
        $disable=false;
      $html.="<span onclick=\"document.editForm.sort_".$field_id."_methode.value='DESC';document.editForm.submit();\">".maakKnop('sort_az_ascending.png',array('size'=>16,'disabled'=>$disable))." </span>";
      // if($selectedValue<>'DESC')
      if($selectedValue=='DESC')
        $disable=true;
      else
        $disable=false;
      $html.="&nbsp;&nbsp;<span onclick=\"document.editForm.sort_".$field_id."_methode.value='ASC';document.editForm.submit();\">".maakKnop('sort_az_descending.png',array('size'=>16,'disabled'=>$disable))." </span>";

      //$html="<select name=\"".$field_id."_methode\">\n";
      //$html .= "</select>";
      return $html;
    }
    else
    {
      $options=array(
        "gelijk"        =>vt("gelijk aan"),
        "nietGelijk"    =>vt("niet gelijk aan"),
        "groter"        =>vt("groter dan"),
        "kleiner"       =>vt("kleiner dan"),
        "groterGelijk"  =>vt("groter of gelijk dan"),
        'kleinerGelijk' =>vt("kleiner of gelijk dan"),
        'bevat'         =>vt('bevat'),
        'bevatniet'     =>vt('bevat niet'),
        'begintmet'     =>vt('begint met'),
        'eindigtmet'    =>vt('eindigt met'),
        'inlijst'       =>vt('in lijst'),
        'isnull'        =>vt('leeg'),
        'isnotnull'     =>vt('niet leeg')
      );
      $field_id = 'filter_'.$field_id;
    }

    $html="<select name=\"".$field_id."_methode\">\n";
    foreach ($options as $key=>$omschrijving)
    {
      if($key == $selectedValue)
        $selected = "SELECTED";
      else
        $selected = "";
      $html.= "<option value=\"$key\"$selected>$omschrijving </option>\n";
    }
    $html .= "</select>";
    return $html;
  }

  function printHeader($disableEdit=false)
  {

    /*
      <colgroup>
    <col />
    <col />
    <col />
    <col />
    <col />

    <col />
    <col class="noSelectable"/>
  </colgroup>

    */

    $head = "<colgroup><col style=\"width:30px\" />";
    $output  = "<tr class=\"list_kopregel\" >\n";
    if (!$disableEdit)
    {
      $output .= "<td class=\"list_button\">&nbsp;</td>\n";
    }

    if($this->group==true)
    {
      $head .= "<col style=\"width:30px\" />";
      $output .= "<td class=\"list_button\">Aantal</td>\n";
    }

    // rebuild querystring :  zonder sort, desc  zonder page
    foreach($this->queryString as $keyname => $value)
    {
      if($keyname != "sort" && $keyname != "direction" && $keyname != "page")
        $str .= "&".urlencode($keyname)."=".urlencode($value);
    }

    for($b=0;$b < count($this->columns); $b++)
    {
      $column = $this->columns[$b];
      if (is_object($this->objects[$column['objectname']]))
        $column['options'] = array_merge($this->objects[$column['objectname']]->data['fields'][$column['name']],$column['options']);

      if($this->idField != $this->objects[$column['objectname']]->data['table'].".".$column['name'] && $column['options']['list_invisible'] == false)
      {
        $field=$this->objects[$column['objectname']]->data['table'].".".$column['name'];

//			  $savedWidth=$_SESSION[$_SESSION['tableSettings']['currentTable']]['widths'][$field];
//			  if($savedWidth <> '')
//			    $column['options']['list_width']=$savedWidth;
//			  else
        if(empty($column['options']['list_width']))
          $column['options']['list_width']="150";
//			    else
//		  	    $column['options']['list_width'];


//  	    $this->objects[$column['objectname']]->data['fields'][$column['name']]['list_width']=$column['options']['list_width'];


        $head .= '<col style="width:'.$column['options']['list_width'].'px"/>';
        if(!$this->objects[$column['objectname']] && $column['objectname'] != "")
        {
          $this->objects[$column['objectname']] =  new $column['objectname']();
        }
        $table = $this->objects[$column['objectname']]->data['table'];

        // veldnaam zonder table. als het een alias is!
        if($column['options']['sql_alias'])
        {
          $fieldName = ".".$column['name'];
        }
        else
        {
          $fieldName = $table.".".$column['name'];
        }

        $dir = "DESC";
        if($key = array_search($fieldName,$this->queryOrder))
        {
          if($this->queryDirection[$key] == "DESC")
            $dir = "ASC";
        }

        foreach ($this->filterOptions as $item=>$data);
        {
          if(isset($column['options']['list_search']))
            $filter = "  <a href=\"#\" onclick=\"document.editForm.addFilter.value='".$fieldName."';document.editForm.submit();return false;\" >".maakKnop('funnel.png',array('size'=>16))."</a> ";//<img alt=\"Filter\" src=\"images/trechterk.png\" width=\"12\" height=\"14\" border=\"0\">
        }

        $search =  $filter;
        $search .= ($column['options']['search'])?maakKnop('view.png',array('size'=>16)):"";
        $title = ($column['options']['description'])?$column['options']['description']:$column['name'];
        $title = vt($title);
				$output .= "<td data-table=\"$table\" data-field=\"".$column['name']."\" class=\"list_kopregel_data\">".$search." ";
        // zoeken en sorteren?
        if(!empty($column['objectname']) || !empty($column['options']['sql_alias']))
        {
          if($column['options']['list_order'])
          {
            $output .= "  <a href=\"#\" onclick=\"document.editForm.addSort.value='".$fieldName."';document.editForm.submit();return false;\" >".maakKnop('sort_az_descending.png',array('size'=>16)) ." "; //<img alt=\"Sortering\" src=\"images/az_o.gif\" width=\"12\" height=\"14\" border=\"0\">
          }
          else
          {
            $output .=  "<a href=\"?".$str."&selectie=".mysql_escape_string($this->searchString)."\">";
          }
          if($this->noGroup==false)
            $output .= "  <a href=\"#\" onclick=\"document.editForm.addGroup.value='".$fieldName."';document.editForm.submit();return false;\" >".maakKnop('index.png',array('size'=>16)) ." "; //<img alt=\"Sortering\" src=\"images/az_o.gif\" width=\"12\" height=\"14\" border=\"0\">

        }
        $output .= $title;

        if(!empty($column['objectname']) || !empty($column['options']['sql_alias']))
          $output .= "</a>";

        $output .= "</td>\n";
      }

    }
    $output  .= "</tr>\n";
    $head.="</colgroup>\n";
    return $head.$output;
  }

  function buildRow($data, $template="", $options="")
  {

    if(empty($template))
    {
      if ($data['tr_class'])
        $trClass = $data['tr_class'];
      else
        $trClass = "list_dataregel";

      if ($data['tr_style'])
        $trStyle = $data['tr_style'];
      else
        $trStyle = "";

      if ($data['tr_title'])
        $trTitle = $data['tr_title'];
      else
        $trTitle = vt("Klik op de knop links om de details te zien/muteren");

      if($data['extraqs'])
        $extraqs = "&".$data['extraqs'];

		    $output  = "<tr data-lineId=\"" . $data[$this->idField]['value'] . "\" class=\"".$trClass."\" onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='".$trClass."'\" title=\"".$trTitle."\">\n";
      if (!$data['disableEdit'])
      {
        $output .= "<td class=\"list_button\">";

        if($this->customEdit == true)
        {
          $output .= "<div class=\"icon\"><a href=\"javascript:editRecord('".$this->editScript."?action=edit&".$this->idField."=".$data[$this->idField]['value'].$extraqs."');\">".drawButton("edit")."</a></div>";
        }
        else
        {//
          if(isset($this->fullEditScript))
          {
  		      $output = "<tr data-lineId=\"" . $data[$this->idField]['value'] . "\" class=\"".$trClass."\" $trStyle onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='".$trClass."'\" title=\"".$trTitle."\" >\n".
              " <td  ><a href=\"".str_replace('{id}',$data[$this->idField]['value'],$this->fullEditScript)."\" class=\"icon\">".drawButton("edit")."</a>"; //width=200
          }
          else
      		    $output = "<tr data-lineId=\"" . $data[$this->idField]['value'] . "\" class=\"".$trClass."\" $trStyle onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='".$trClass."'\" title=\"".$trTitle."\" >\n".
              " <td ".$this->editIconTd."><a href=\"".$this->editScript."?action=edit&id=".$data[$this->idField]['value']."\" class=\"icon\">".drawButton("edit")."</a>";

          //onclick=\"document.location='".$this->editScript."?action=edit&id=".$data[$this->idField][value]."'\"
        }
        $output .= $this->editIconExtra;
        $output .= "</td>\n";
      }
    }
    else
    {
			$template = str_replace( "{".$this->idField."_value}", '<span name="{'.$this->idField.'}">'.$data[$this->idField]['value'].'</span>', $template);
    }

    if($this->group==true)
    {
      $printdata.=$data['aantalRecords']['value'];
      $output .= "<td class=\"listTableData\" align=\"right\">$printdata &nbsp;";
      unset($data['aantalRecords']);
    }

    $noClick = $data["noClick"];

    foreach($data as $key=>$row)
    {

      if($this->idField == $key || $key =='disableEdit')
        continue;

      $width = "";
      $align = "";

      switch($row['form_type'])
      {
        case "checkbox" :
          if($row['value']==="0" || $row['value'] > 0)
            $printdata = imagecheckbox($row['value']);
          else
            $printdata = 'leeg';
          break;
        case "datetime" :
          $printdata = date('d-m-Y H:i:s', db2jul($row['value']));
          break;
        case "datum" :
        case "calendar" :
          $printdata = dbdate2form($row['value']);
          break;
        default :
          $printdata = $row['value'];
          break;
      }

      if(is_array($row['list_conversie']))
        $printdata=$row['list_conversie'][$printdata];

      if($row['list_format'])
      {
        $printdata = sprintf($row['list_format'], $printdata);
      }

      if ($row['td_style'])
        $style = $row['td_style'];
      else
        $style = "";

      if($row['list_numberformat'])
      {
        if($row['list_numberformatZonderNullen'])
        {
          $getal = explode('.',$printdata);
          $decimaalDeel = $getal[1];
          for ($i = strlen($decimaalDeel); $i >=0; $i--)
          {
            $decimaal = $decimaalDeel[$i-1];
            if ($decimaal != '0' && !$newDec)
            {
              $newDec = $i;
            }
          }
          $printdata=number_format($printdata,$newDec,",",".");

        }
        else
          $printdata=number_format($printdata,$row['list_numberformat'],',','.');
      }

      if($row["list_money"])
      {
        $out  = "";
        $out2 = "";
        if ($printdata < 0)
        {
          $out  = '<span style="color: Red">';
          $out2 = '</span>';
        }
        $decimals = is_numeric($row["list_money"])?$row["list_money"]:null;
        $printdata = number_format($printdata,$decimals,$__appvar["dec_seperator"],$__appvar["thou_seperator"]);
        $printdata = $out.$printdata.$out2;
        $row['list_nobreak']=true;
      }

      if(empty($template) && ($row['list_invisible'] == false))
      {
        if(!empty($row['list_width']))
        {
          $width = "width=\"".$row['list_width']."\"   ";
          /*
                    $table=$this->objects[$this->columns[$a][objectname]]->data['table'];

                  $savedWidth=$_SESSION[$_SESSION['tableSettings']['currentTable']]['widths'][$row['table'].".".$row['field']];
                  if($savedWidth <> '')
                    $row['list_width'] = $savedWidth;
          */
          if($row['list_extraEvalLink'])
          {
            $extraLink=eval($row['list_extraEvalLink']);
          }
          $maxChar = $row['list_width'] /	$this->pixelsPerChar;

          if(strlen($printdata) > $maxChar && $row['form_type'] != 'checkbox' && $row['list_nobreak'] != true)
          {
            $printdata = substr($printdata,0,$maxChar)."...";
          }

        }
        if(!empty($row['list_align']))
        {
          $align = "align=\"".$row['list_align']."\"";
        }

        if ( isset ($row['url']) && ! empty ($row['url']) ) {
          $row['noClick'] = false;
        }
//			if ( $key === 'noClick' ) {continue;}
//				debug($key);
//				debug($row);
//debug($row);
        if( (isset($row['noClick']) && $row['noClick'] == true) OR ($noClick) OR ( $this->noClick === true && ( ! isset ($row['noClick']) || $row['noClick'] == true) ) )
        {
          $output .= "<td data-field=\"".$row['field']."\" class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode'].">$printdata &nbsp;";
        }
        elseif ( isset ($data[$key]['url']) && ! empty ($data[$key]['url']) && $data[$this->idField]['value'] > 0 )
        {
					$output .= "<td data-field=\"".$row['field']."\" class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode']."><a href=\"".str_replace('{id}',$data[$this->idField]['value'],$data[$key]['url'])."\" >$printdata &nbsp;</a></td>\n";
        }
        elseif($data[$this->idField]['value'] > 0)
        {
          if(isset($this->fullEditScript) && $this->fullEditScript <> '')
          	$output .= "<td data-field=\"".$row['field']."\" class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode']."><a href=\"".str_replace('{id}',$data[$this->idField]['value'],$this->fullEditScript)."\" >$printdata &nbsp;</a></td>\n";
          elseif($this->customEdit == true)
						$output .= "<td data-field=\"".$row['field']."\" class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode']."><a href=\"javascript:editRecord('".$this->editScript."?action=edit&".$this->idField."=".$data[$this->idField]['value'].$extraqs."');\">$printdata &nbsp;</a></td>";
          else
    	    	$output .= "<td data-field=\"".$row['field']."\" class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode']."><a href=\"".$this->editScript."?action=edit&id=".$data[$this->idField]['value'].$extraLink."\" >$printdata &nbsp;</a></td>\n";
        }
        else
    	    $output .= "<td data-field=\"".$row['field']."\" class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode'].">$printdata &nbsp;";
      }

      $template = str_replace( "{".$row[field]."_value}", $printdata, $template);
    }
    if(empty($template))
    {
      $output .= "</tr>\n";
    }
    else
    {
      $output = $template;
    }


    return $output;
  }

  function printRow($template="")
  {
    if ($data = $this->getRow())
    {
      return $this->buildRow($data,$template,"");
    }
    return false;
  }


  function fillTemplate($data, $template)
  {
    return $this->buildRow($data,$template,"");
  }

  function setXLS($title='')
  {
    include_once("AE_cls_xls.php");
    $this->xlsData = array();
    $this->xls = new AE_xls();
    $this->selectAllPage();

    $this->perPage = 64000;
    if(trim($title) <> '')
      $this->xlsData[][]=$title;
    $row=count($this->xlsData);
    for($x=0;$x<count($this->columns);$x++)
    {
      $this->xlsData[$row][]=array($this->columns[$x]['name'],'header');
    }
    $x=count($this->xlsData);
    while($data = $this->getRow())
    {
      foreach ($data as $key=>$dataArray)
      {
        if($key != $this->idField)
        {

          if(($dataArray['form_type'] == 'calendar' || $dataArray['form_type'] == 'datum') && $dataArray['value'] <> '' && substr($dataArray['value'],0,10) <> '0000-00-00')
          {
            $tijd=substr($dataArray['value'],11,8);
            $datum=substr($dataArray['value'],0,10);
            $datumParts=explode("-",$datum);
            if(count($datumParts)==3)
              $datum=$datumParts[2]."-".$datumParts[1]."-".$datumParts[0];

            if($tijd <> '' && $tijd <> '00:00:00')
              $datum.=' '.$tijd;
            else
              $datum=round((adodb_db2jul($dataArray['value'])+(86400 * 25569))/86400);
            // Unix timestamp to Excel date difference in seconds
            //$ut_to_ed_diff = 86400 * 25569;
            $this->xlsData[$x][]=array($datum,'date');
          }
          else {
            $xlsVal = $dataArray['value'];
            if ( isset ($dataArray['value'][0]) && $dataArray['value'][0] === '=') {
              $xlsVal = ' ' . $xlsVal;
            }
            $this->xlsData[$x][] = array($xlsVal, 'body');
          }
        }
      }
      $x++;
    }
    $this->xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
    $this->xls->excelOpmaak['body']=array('setBorder'=>'1');
    $this->xls->excelOpmaak['date']=array('setBorder'=>'1','setBgColor'=>'22','setNumFormat'=>'DD-MM-YYYY');
    $this->xls->setColumn[]=array(0,0,6);
    $this->xls->setColumn[]=array(2,5,12);
    $this->xls->setData($this->xlsData);

  }

  function getXLS()
  {
    //$type='xls';
    if(class_exists('XMLWriter'))
      $type='xlsx';
    else
      $type='xls';

    if ($this->xlsFilename <> "")
    {
      if(substr($this->xlsFilename,-3)=='xls')
        $type='xls';
      $filename = $this->xlsFilename;
    }
    else
    {
      $filename = 'xls_' . date('Ymd') . '.' . $type;

      if ($type == 'xlsx')
      {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
      }

    }
    if($type=='xls')
      $toFile=false;
    else
      $toFile=true;

    $this->xls->OutputXls($filename,$toFile,$type);
  }

  function selectAllPage()
  {
    if(empty($page))
      $page = 1;

    $this->page = $page;
    $this->DB = new DB($this->dbId);

    $query = $this->getSQL();
    $pos = strpos($query,"LIMIT");
    if($pos > 0)
      $query = substr($query,0,$pos);
    $this->DB->SQL($query);
    return $this->DB->Query();
  }
}




class MysqlList
{
	var $BTR_REMOVE_QUERY_LIMIT;

  var $tables;
  var $columns;
  var $objects;


  var $queryWhere;
  var $querySelect;
  var $queryOrder;
  var $queryDirection;
  var $queryJoin;
  var $queryGroupBy;

  var $searchString;
  var $searchFields;


  var $queryString;

  var $page;
  var $perPage;

  var $editScript;

  var $customEdit;

  var $DB;

  function MysqlList()
  {
		global $__BTR_CONFIG;
		$this->BTR_REMOVE_QUERY_LIMIT 			  = $__BTR_CONFIG["REMOVE_QUERY_LIMIT"];

    // init stuff
    $this->tables         = array();
    $this->columns         = array();
    $this->objects        = array();
    $this->queryOrder     = array();
    $this->queryDirection = array();
    $this->searchFields   = array();
    $this->searchString   = "";
    $this->sqlQuery       = "";
    $this->queryString    = $_GET;
    $this->perPage        = 25;
    $this->RowCounter     = 0;
    $this->Records        = 0;
    $this->page           = 1;
    $this->dbId           = 1;
    $this->DB = new DB($this->dbId);
  }

  // depricated function
  function addField($objectName, $column, $options=array())
  {
    $this->addColumn($objectName, $column, $options);
  }

  function addColumn($objectName, $column, $options=array())
  {
    $tmp['name'] 		 	= $column;
    //$tmp['table'] 		 = $this->object[$objectName]->data[table]; // hier word niks mee gedaan?
    $tmp['objectname'] = $objectName;
    $tmp['options'] 	 = $options;
    array_push($this->columns,$tmp);
  }

  function removeColumn($column)
  {
    $tmpArray = array();
    for($x=0; $x < count($this->columns);$x++)
    {
      if ($this->columns[$x][name] <> "$column")
      {
        $tmpArray[] = $this->columns[$x];
      }

    }
    $this->columns = $tmpArray;
  }

  function setSelect($txt)
  {
    $this->querySelect = $txt;
  }

  function setWhere($where)
  {
    $this->queryWhere = $where;
  }

  function setSearch($search)
  {
    $this->searchString = $search;
  }

  function setOrder($sort,$direction)
  {
    for($a=0;$a < count($sort); $a++)
    {
      $this->queryOrder[$a+1] = $sort[$a];
      $this->queryDirection[$a+1] = $direction[$a];
    }
  }

  function setJoin($join)
  {
    $this->queryJoin = $join;
  }

  function setGroupBy($groupBy)
  {
    $this->queryGroupBy = $groupBy;
  }

  function getSQL()
  {

    // build table select
    for($a=0;$a < count($this->columns); $a++)
    {
      if(!$this->objects[$this->columns[$a]["objectname"]] && $this->columns[$a]["objectname"] != "")
      {
        $objectName = $this->columns[$a]["objectname"];
        $this->objects[$this->columns[$a]["objectname"]] =  new $objectName();
      }

      if($this->columns[$a]["objectname"] != "")
      {
        // dit is een database veld, maak een query selectie item (en add table)
        $selection[] = $this->objects[$this->columns[$a]["objectname"]]->data['table'].".".$this->columns[$a]["name"];
        $tables[] = $this->objects[$this->columns[$a]["objectname"]]->data['table'];
      }
      else
      {
        if($this->columns[$a]["options"]["sql_alias"])
        {
          // maak een database alias aan

          $selection[] = $this->columns[$a]["options"]["sql_alias"]." AS ".$this->columns[$a]["name"];
        }
        else
        {
          // dit is geen database veld zet hem niet in de selectie.
          //$selection[] = $this->columns[$a][name];
        }
      }
    }
    $tables = array_unique($tables);

    if(isset($this->forceSelect))
      $query  = $this->forceSelect;
    else
      $query  = "SELECT ".implode(", ",$selection);

    if(isset($this->forceFrom))
      $query  .= $this->forceFrom;
    else
      $query .= "  FROM (".implode(", ",$tables).") ";

    if($this->queryJoin)
      $query .= " ".$this->queryJoin." ";

    if(isset($this->forceWhere))
      $query  .= $this->forceWhere;
    else
    {
      $query .= " WHERE 1 ";
      if ($this->queryWhere)
      {
        $query .= " AND ".$this->queryWhere." ";
      }
    }

    // set Where
    if($this->searchString)
    {
      for($a=0; $a < count($this->columns); $a++)
      {
        if($this->columns[$a]["options"]["search"] == true)
        {
          if($this->columns[$a]["options"]["sql_alias"])
          {
            $fieldName = $this->columns[$a]["options"]["sql_alias"];
          }
          else
          {
            if(isset($this->noTables))
              $fieldName = $this->columns[$a]['name'];
            else
              $fieldName = $this->objects[$this->columns[$a]['objectname']]->data['table'].".".$this->columns[$a]['name'];
          }

          $search[] = $fieldName." LIKE '%".mysql_escape_string($this->searchString)."%'";
        }
      }
      if(is_array($search))
      {
        $searchString = implode(" OR ",$search);
        $query .= " AND (".$searchString.") ";
      }
    }

    // group by
    if($this->queryGroupBy)
      $query .= " GROUP BY ".$this->queryGroupBy;

    // order by
    for($a=1; $a <= count($this->queryOrder); $a++)
    {
      $order[] = $this->queryOrder[$a]." ".$this->queryDirection[$a];
    }
    if($order)
      $query .= " ORDER BY ".implode(", ",$order);


    // limit perPage
		$from = $this->BTR_REMOVE_QUERY_LIMIT ? 0 : ($this->page-1) * ($this->perPage);
    $this->from = $from;
//		$query .= " LIMIT ".$from.",".$this->perPage;
    $this->sqlQuery = $query;
    //echo $query;//exit;
    return $query;
  }

  function selectPage($page)
  {
    if(empty($page))
      $page = 1;

    $this->page = $page;
//    $this->DB = new DB($this->dbId);
    $this->DB->SQL($this->getSQL());
    return $this->DB->Query();
  }

  function recordsO()
  {
    if($query = $this->getSQL())
    {
      $pos = strpos($query,"LIMIT");
      $qs = substr($query,0,$pos);
      $tmp = new DB($this->dbId);
      $tmp->SQL($qs);
      $tmp->Query();
      return $tmp->Records();
    }
    return 0;
  }

  function records()
  {
    if($query = $this->getSQL())
    {
      if (!$this->DB->resultReady())
      {
        $this->DB->SQL($query);
        $this->DB->Query();
      }

      $this->Records = $this->DB->Records();
      if ($this->Records > 0 )
        $this->DB->gotoRow($this->from);
      return $this->Records;
    }
    return 0;
  }

  //  returns fetcharray
  function getRow()
  {
    $this->RowCounter++;

	  if( $this->RowCounter > ($this->BTR_REMOVE_QUERY_LIMIT ? 100000 : $this->perPage))
      return false;

    if($result = $this->DB->nextRecord())
    {
      for($b=0;$b < count($this->columns); $b++)
      {
        $column = $this->columns[$b];

        if(!$this->objects[$column["objectname"]] && $column["objectname"] != "")
        {
          $this->objects[$column["objectname"]] =  new $column["objectname"]();
        }

        // haal default options uit object.
        if($column["objectname"] != "")
        {
          $options = $this->objects[$column["objectname"]]->data['fields'][$column["name"]];
        }

        $data[$column["name"]] = $column["options"];
        $data[$column["name"]]["field"] = $column["name"];
        $data[$column["name"]]["value"] = $result[$column["name"]];

        if($column["objectname"] != "")
        {
          $data[$column["name"]] = array_merge($options,$data[$column["name"]]);
        }
      }
      return $data;
    }
    else
      return false;
  }

  function printHeader($disableEdit=false)
  {
    $output  = "<tr class=\"list_kopregel\">\n";
    if (!$disableEdit)
    {
      $output .= "<td class=\"list_button\">&nbsp;</td>\n";
    }

    // rebuild querystring :  zonder sort, desc  zonder page
    foreach($this->queryString as $keyname => $value)
    {
      if($keyname != "sort" && $keyname != "direction" && $keyname != "page")
        $str .= "&".urlencode($keyname)."=".urlencode($value);
    }

    $head = "<colgroup><col />";


    for($b=0;$b < count($this->columns); $b++)
    {
      $column = $this->columns[$b];

      if (is_object($this->objects[$column['objectname']]))
        $column['options'] = array_merge($this->objects[$column['objectname']]->data['fields'][$column["name"]],$column['options']);

      if($this->idField != $column['name'] && $column['options']['list_invisible'] == false)
      {
        if(empty($column['options']['list_width']))
          $column['options']['list_width']=150;
        $head .= '<col width="'.$column['options']['list_width'].'"/>';

        if(!$this->objects[$column['objectname']] && $column['objectname'] != "")
        {
          $this->objects[$column['objectname']] =  new $column['objectname']();
        }
        $table = $this->objects[$column['objectname']]->data['table'];

        // veldnaam zonder table. als het een alias is!
        if($column['options']['sql_alias'])
        {
          $fieldName = $column['name'];
        }
        else
        {
          if(isset($this->noTables))
            $fieldName = $column['name'];
          else
            $fieldName = $table.".".$column['name'];
        }

        $dir = "DESC";
        if($key = array_search($fieldName,$this->queryOrder))
        {
          if($this->queryDirection[$key] == "DESC")
            $dir = "ASC";
        }

        //rvv $search = ($column['options']['search'])?".":"";
        //		$search = ($column['options']['search'])?"<img src=\"images/search.gif\" width=\"13\" height=\"14\" border=\"0\">":"";
        $search = ($column['options']['search'])?maakKnop('view.png',array('size'=>16)):"";
        $title = ($column['options']['description'])?$column['options']['description']:$column['name'];
        $title = vt($title);
				$output .= "<td data-table=\"$table\" data-field=\"".$column['name']."\" class=\"list_kopregel_data\">".$search." ";
        // zoeken en sorteren?
        if(!empty($column['objectname']) || !empty($column['options']['sql_alias']))
        {

          if($column['options']['list_order'])
          {
            $output .=  "<a href=\"?".$str."&sort[]=".$fieldName."&direction[]=".$dir."&selectie=".$this->searchString."\">";
            $output .=  maakKnop('sort_az_descending.png',array('size'=>16))."&nbsp;";
          }
          else
          {
            $output .=  "<a href=\"?".$str."&selectie=".$this->searchString."\">";
          }

        }
        $output .= $title;

        if(!empty($column['objectname']) || !empty($column['options']['sql_alias']))
          $output .= "</a>";

        $output .= "</td>\n";
      }

    }
    $head.="</colgroup>\n";
    $output  .= "</tr>\n";
    return $head.$output;
  }

  function buildRow($data, $template="", $options="")
  {
    if(empty($template))
    {
      if ($data["tr_class"])
        $trClass = $data["tr_class"];
      else
        $trClass = "list_dataregel";

      if ($data["tr_title"])
        $trTitle = $data["tr_title"];
      else
        $trTitle = vt("Klik op de knop links om de details te zien/muteren");

      if($data["extraqs"])
        $extraqs = "&".$data["extraqs"];

      $output  = "<tr data-lineId=\"" . $data[$this->idField]['value'] . "\" class=\"".$trClass."\" onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='".$trClass."'\" title=\"".$trTitle."\">\n";
      if (!$data["disableEdit"])
      {
        $output .= "<td class=\"list_button\">";

        if($this->customEdit == true)
        {
          $output .= "<div class=\"icon\"><a href=\"javascript:editRecord('".$this->editScript."?action=edit&".$this->idField."=".$data[$this->idField]['value'].$extraqs."');\">".drawButton("edit")."</a></div>";
        }
        else
        {
          $output .= "<div class=\"icon\"><a href=\"".$this->editScript."?action=edit&".$this->idField."=".$data[$this->idField]['value'].$extraqs."\">".drawButton("edit")."</a></div>";
        }
        $output .= "</td>\n";
      }
    }
    else
    {
      $template = str_replace( "{".$this->idField."_value}", $data[$this->idField]["value"], $template);
    }

    foreach($data as $row)
    {
      if(is_array($row))
      {
        $width = "";
        $align = "";

        switch($row["form_type"])
        {
          case "checkbox" :
            $printdata = imagecheckbox($row["value"]);
            break;
          case "datum" :
          case "calendar" :
            $printdata = dbdate2form($row["value"]);
            break;
          default :
            $printdata = $row["value"];
            break;
        }

        if($row['list_format'])
        {
          $printdata = sprintf($row['list_format'], $printdata);
        }

        if($row['list_numberformat'])
          $printdata=number_format($printdata,$row['list_numberformat'],',','.');

        if ($row["td_style"])
          $style = $row["td_style"];
        else
          $style = "";

        if(empty($template) && ($this->idField != $row["field"] && $row["list_invisible"] == false))
        {
          if(!empty($row["list_width"]))
          {
            $width = "width=\"".$row["list_width"]."\"";
          }

          if(!empty($row["list_align"]))
          {
            $align = "align=\"".$row["list_align"]."\"";
          }
          $output .= "<td data-field=\"" . $row["field"] . "\" class=\"listTableData\" ".$width." ".$style." ".$align." ".$row["list_tdcode"].">";
          $output .= $printdata." &nbsp;";
          $output .= "</td>\n";
        }

        $template = str_replace( "{".$row["field"]."_value}", $printdata, $template);
      }
    }
    if(empty($template))
    {
      $output .= "</tr>\n";
    }
    else
    {
      $output = $template;
    }
    return $output;
  }

  function printRow($template="")
  {
    if ($data = $this->getRow())
    {
      return $this->buildRow($data,$template,"");
    }
    return false;
  }


  function fillTemplate($data, $template)
  {
    return $this->buildRow($data,$template,"");
  }

  function setXLS($title='')
  {
    include_once("AE_cls_xls.php");
    $this->xlsData = array();
    $this->xls = new AE_xls();
    $this->selectAllPage();




    for($x=0;$x<count($this->columns);$x++)
    {
      $this->xlsData[0][]=array($this->columns[$x]['name'],'header');
    }
    $x=1;
    while($data = $this->getRow())
    {
      foreach ($data as $key=>$dataArray)
      {
        $this->xlsData[$x][]=array($dataArray['value'],'body');
      }
      $x++;
    }
    $this->xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
    $this->xls->excelOpmaak['body']=array('setBorder'=>'1');
    $this->xls->setColumn[]=array(0,0,6);
    $this->xls->setColumn[]=array(2,5,12);
    $this->xls->setData($this->xlsData);
  }

  function getXLS()
  {
    $this->xls->OutputXls('xls_'.date('Ymd').'.xls');
  }

  function selectAllPage()
  {
    $this->DB = new DB($this->dbId);
    $query = $this->getSQL();
    $this->DB->SQL($query);
    return $this->DB->Query();
  }
}