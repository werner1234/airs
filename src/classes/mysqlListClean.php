<?php
class mysqlListClean extends MysqlList2
{
  var $editable = true;

  var $columnOptionsDefaults = array (
    'list_align'        => '',
    'list_invisible'    => false,
    'list_visible'      => true
  );

  function mysqlListClean ()
  {
    $this->MysqlList2();

  }


  function printHeader($disableEdit=false)
  {
    $output = '<thead>';
    $output  .= "<tr class=\"list_kopregel\" >\n";
    if ( ! $disableEdit) {
      $output .= "<th class=\"list_button\">&nbsp;</td>\n";
    }

    if( $this->group == true ) {
      $output .= "<th class=\"list_button\">Aantal</th>\n";
    }

    // rebuild querystring :  zonder sort, desc  zonder page
    $str = '';
    foreach($this->queryString as $keyname => $value)
    {
      if ($keyname != "sort" && $keyname != "direction" && $keyname != "page")
      {
        $str .= "&".urlencode($keyname)."=".urlencode($value);
      }
    }

    foreach ( $this->columns as $key => $column ) {


      $fieldPrefix = $this->objects[$column['objectname']]->data['table'];
      $table = $this->objects[$column['objectname']]->data['table'];

      // veldnaam zonder table. als het een alias is!
      $fieldName = $table . '.' . $column['name'];
      $idField = $table . '.' . $this->idField;
      if($column['options']['sql_alias'])	{
        $fieldName = '.' . $column['name'];
        $idField = $this->idField;
      }

      if (is_object($this->objects[$column['objectname']])) {
        $column['options'] = array_merge($this->objects[$column['objectname']]->data['fields'][$column['name']],$column['options']);
      }
      if (
        $idField != $fieldName &&

        $column['options']['list_invisible'] == false &&
        $column['options']['list_visible'] === true
      ) {
        $field = $fieldPrefix . '.' . $column['name'];

//        if( ! isset ($column['options']['list_width']) || empty ($column['options']['list_width']) ) {
//          $column['options']['list_width']="150";
//        }

        if( ! $this->objects[$column['objectname']] && $column['objectname'] != "") {
          $this->objects[$column['objectname']] =  new $column['objectname']();
        }

        /** set order **/
        $dir = "DESC";
        $orderKey = array_search($fieldName, $this->queryOrder);
        if( $orderKey !== false && ( isset ($this->queryDirection[$key]) && $this->queryDirection[$key] == "DESC") ) {
          $dir = "ASC";
        }

//        foreach ( $this->filterOptions as $item => $filterData ) {
        if(isset($column['options']['list_search']) && $column['options']['list_search'] === true ) {
          $filter = "  <a href=\"#\" onclick=\"document.editForm.addFilter.value='".$fieldName."';document.editForm.submit();return false;\" >".maakKnop('funnel.png',array('size'=>16))."</a> ";//<img alt=\"Filter\" src=\"images/trechterk.png\" width=\"12\" height=\"14\" border=\"0\">
        }


        $search =  $filter;
        $search .= $column['options']['search'] !== false ? maakKnop('view.png',array('size'=>16)) : '';
        $title = ! empty ($column['options']['description']) ? $column['options']['description'] : $column['name'];
        $title = vt($title);
        $output .= '<th 
          align="' . $column['options']['list_align'] . '"
          class="list_kopregel_data">' . $search . '
        ';

        /** zoeken en sorteren? **/
        if( ! empty($column['objectname']) || ! empty($column['options']['sql_alias'])) {
          if($column['options']['list_order']) {
            $output .= "  ".maakKnop('sort_az_descending.png',array('size'=>16)) ." "; //<img alt=\"Sortering\" src=\"images/az_o.gif\" width=\"12\" height=\"14\" border=\"0\">
          }

          if($this->noGroup === false) {
            $output .= "  <a href=\"#\" onclick=\"document.editForm.addGroup.value='".$fieldName."';document.editForm.submit();return false;\" >".maakKnop('index.png',array('size'=>16)) ." "; //<img alt=\"Sortering\" src=\"images/az_o.gif\" width=\"12\" height=\"14\" border=\"0\">
          }
        }
        $output .= $title;

        if( ! empty($column['objectname']) || ! empty($column['options']['sql_alias'])) {
          $output .= "</a>";
        }

        $output .= "</th>\n";
      }
    }
    $output .= '</thead>';
    $output  .= "</tr>\n";
    return $output;
  }

  function buildRow($data, $template="", $options="")
  {
    if(empty($template))
    {

      $trClass = "list_dataregel";
      if ($data['tr_class']) {$trClass = $data['tr_class'];}

      $trTitle = "Klik op de knop links om de details te zien/muteren";
      if ( isset($data['tr_title']) ) {$trTitle = $data['tr_title'];}

      if( isset($data['extraqs']) ) {$extraqs = "&".$data['extraqs'];}

      $output  = "<tr class=\"".$trClass."\" title=\"".$trTitle."\">\n";


      if ( ! $data['disableEdit'] && $this->editable === true)
      {
        $output .= "<td class=\"list_button\">";

        if($this->customEdit == true)
        {
          $output .= "<div class=\"icon\"><a href=\"javascript:editRecord('".$this->editScript."?action=edit&".$this->idField."=".$data[$this->idField]['value'].$extraqs."');\">".drawButton("edit")."</a></div>";
        }
        else
        {
          if(isset($this->fullEditScript))
          {
            $output = "<tr class=\"".$trClass."\" title=\"".$trTitle."\" >\n".
              " <td  ><a href=\"".str_replace('{id}',$data[$this->idField]['value'],$this->fullEditScript)."\" class=\"icon editButton\">".drawButton("edit")."</a>"; //width=200
          }
          else
            $output = "<tr class=\"".$trClass."\" title=\"".$trTitle."\" >\n".
              " <td ".$this->editIconTd."><a href=\"".$this->editScript."?action=edit&id=".$data[$this->idField]['value']."\" class=\"icon editButton\">".drawButton("edit")."</a>";
        }
        $output .= $this->editIconExtra;
        $output .= "</td>\n";
      }
    }
    else
    {
      $template = str_replace( "{".$this->idField."_value}", $data[$this->idField]['value'], $template);
    }

    if($this->group==true)
    {
      $printdata.=$data['aantalRecords']['value'];
      $output .= "<td class=\"listTableData\" align=\"right\">$printdata &nbsp;";
      unset($data['aantalRecords']);
    }
    foreach($data as $key=>$row)
    {
      if($this->idField == $key || $key =='disableEdit' || $row['list_visible'] == false )
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

        if(isset($row['noClick']) && $row['noClick'] == true)
        {
          $output .= "<td class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode'].">$printdata &nbsp;";
        }
        elseif($data[$this->idField]['value'] > 0)
        {
          if(isset($this->fullEditScript) && $this->fullEditScript <> '')
          {
            $output .= "<td class=\"listTableData\" " . $width . " " . $style . " " . $align . " " . $row['list_tdcode'] . "><a href=\"" . str_replace('{id}', $data[$this->idField]['value'], $this->fullEditScript) . "\" >$printdata &nbsp;</a></td>\n";
          }
          else
          {
            $output .= "<td class=\"listTableData\" " . $width . " " . $style . " " . $align . " " . $row['list_tdcode'] . "><a href=\"" . $this->editScript . "?action=edit&id=" . $data[$this->idField]['value'] . $extraLink . "\" >$printdata &nbsp;</a></td>\n";
          }
        }
        else
          $output .= "<td class=\"listTableData\" ".$width." ".$style." ".$align." ".$row['list_tdcode'].">$printdata &nbsp;";
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



}