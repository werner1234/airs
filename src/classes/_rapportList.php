<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2016/05/20 14:19:45 $
 		File Versie					: $Revision: 1.4 $

 		$Log: _rapportList.php,v $
 		Revision 1.4  2016/05/20 14:19:45  rm
 		Htmlrapport
 		
 		Revision 1.3  2016/04/13 13:09:07  cvs
 		call 4833, popup extra info scherm
 		
 		Revision 1.2  2016/04/13 06:32:17  rm
 		no message
 		
 		Revision 1.1  2016/03/30 09:58:01  cvs
 		eerste commit
 		
*/

/**
 * Class rapportList
 */
class rapportList
{
  var $mainObject;
  var $tables;
  var $columns;
  var $objects;
  var $defaultObject;

  var $queryWhere;
  var $querySelect;
  var $queryOrder;
  var $queryDirection;
  var $queryJoin;
  var $queryGroupBy;

  var $searchString;
  var $searchFields;

  var $sort;
  var $sortData;

  var $queryString;

  var $orderBreakArray = array();
  var $orderBreak      = array();

  var $reportTotals = array();
  var $breakTotals  = array();   // subtotalen v/e sectie
  var $sumColumns   = array();

  var $columnExtraData = array();
  var $visibleColumns;
  var $page;
  var $perPage;

  var $editScript;

  var $customEdit;
  var $debug;
  var $DB;
  var $db;


  function rapportList($rapportClass)
  {
    // init stuff
    
    $file = getcwd() . "/../../classes/htmlReports/" . $rapportClass . ".php";
    
    if (!file_exists($file))
    {
      echo "rapportClass niet geledig";
      exit;
    }
    include_once($file);
    $this->mainObject = new $rapportClass();
    $this->sortData = $this->mainObject->sortData;
    $this->defaultObject = $rapportClass;
    $this->tables[] = $this->mainObject->tableName;
    $this->columns = array();
    $this->objects = array();
    $this->queryOrder = array();
    $this->queryDirection = array();
    $this->searchFields = array();

    $this->searchString = "";
    $this->sqlQuery = "";
    $this->queryString = $_GET;
    $this->perPage = 25;
    $this->RowCounter = 0;
    $this->Records = 0;
    $this->page = 1;
    $this->dbId = 1;

    $this->DB = new DB($this->dbId);
    $this->db = new DB($this->dbId);
    $this->debug = false;

  }


  function getPropArray($field)
  {
    return $this->mainObject->data[$field];
  }
  
  
  function addColumn($objectName, $column, $options = array())
  {
    $tmp['name'] = $column;
    $tmp['objectname'] = $objectName;
    $tmp['options'] = $options;
    array_push($this->columns, $tmp);
    if (count($options) > 0)
    {
      $this->columnExtraData[$column] = $options;
    }
  }

  function removeColumn($column)
  {
    $tmpArray = array();
    for ($x = 0; $x < count($this->columns); $x++)
    {
      if ($this->columns[$x]["name"] <> "$column")
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

  function setOrder($sort, $direction)
  {
    for ($a = 0; $a < count($sort); $a++)
    {
      $this->sort[$sort[$a]] = $direction[$a];
      $this->queryOrder[$a + 1] = $sort[$a];
      $this->queryDirection[$a + 1] = $direction[$a];
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
    for ($a = 0; $a < count($this->columns); $a++)
    {

      if (!isset($this->objects[$this->columns[$a]["objectname"]]) && $this->columns[$a]["objectname"] != "")
      {
        $objectName = $this->columns[$a]["objectname"];
        $this->objects[$this->columns[$a]["objectname"]] = new $objectName();
        ////debug($this->objects[$this->columns[$a][objectname]],"het object" );
      }

      if ($this->columns[$a]["objectname"] != "")
      {
        // dit is een database veld, maak een query selectie item (en add table)
        $selection[] = $this->objects[$this->columns[$a]["objectname"]]->tableName . "." . $this->columns[$a]["name"];
        $tables[] = $this->objects[$this->columns[$a]["objectname"]]->tableName;
      }
      else
      {
        if ($this->columns[$a]["options"]["sql_alias"])
        {
          // maak een database alias aan

          $selection[] = $this->columns[$a]["options"]["sql_alias"] . " AS " . $this->columns[$a]["name"];
        }
        else
        {
          // dit is geen database veld zet hem niet in de selectie.
          //$selection[] = $this->columns[$a][name];
        }
      }
    }
    $tables = array_unique($tables);

    if (isset($this->forceSelect))
    {
      $query = $this->forceSelect;
    }
    else
    {
      $query = "SELECT " . implode(", ", $selection);
    }

    if (isset($this->forceFrom))
    {
      $query .= $this->forceFrom;
    }
    else
    {
      $query .= "  FROM (" . implode(", ", $tables) . ") ";
    }

    if ($this->queryJoin)
    {
      $query .= " " . $this->queryJoin . " ";
    }

    if (isset($this->forceWhere))
    {
      $query .= $this->forceWhere;
    }
    else
    {
      $query .= " WHERE 1 ";
      if ($this->queryWhere)
      {
        $query .= " AND " . $this->queryWhere . " ";
      }
    }

    // set Where
    if ($this->searchString)
    {
      for ($a = 0; $a < count($this->columns); $a++)
      {
        if ($this->columns[$a]["options"]["search"] == true)
        {
          if ($this->columns[$a]["options"]["sql_alias"])
          {
            $fieldName = $this->columns[$a]["options"]["sql_alias"];
          }
          else
          {
            if (isset($this->noTables))
            {
              $fieldName = $this->columns[$a]['name'];
            }
            else
            {
              $fieldName = $this->objects[$this->columns[$a]['objectname']]->data['table'] . "." . $this->columns[$a]['name'];
            }
          }

          $search[] = $fieldName . " LIKE '%" . mysql_escape_string($this->searchString) . "%'";
        }
      }
      if (is_array($search))
      {
        $searchString = implode(" OR ", $search);
        $query .= " AND (" . $searchString . ") ";
      }
    }

    // group by
    if ($this->queryGroupBy)
    {
      $query .= " GROUP BY " . $this->queryGroupBy;
    }

    foreach ($this->orderBreakArray as $sortingKey)
    {
      if (isset ($this->sortData[$sortingKey]))
      {
        $order[] = $this->sortData[$sortingKey]['orderField'];
      }
    }

    // order by
    for ($a = 1; $a <= count($this->queryOrder); $a++)
    {
      $order[] = $this->queryOrder[$a] . " " . $this->queryDirection[$a];
    }


    if ($order)
    {
      $query .= " ORDER BY " . implode(", ", $order);
    }

    // limit perPage
    $from = ($this->page - 1) * ($this->perPage);
    $this->from = $from;
//		$query .= " LIMIT ".$from.",".$this->perPage;
    $this->sqlQuery = $query;
    //echo $query;//exit;
//		debug($query);
    return $query;
  }


  function selectPage($page)
  {
    if (empty($page))
    {
      $page = 1;
    }

    $this->page = $page;
    $this->DB = new DB($this->dbId);
    $this->DB->SQL($this->getSQL());

//    //debug($this->getSQL(),"getSQL");
    return $this->DB->Query();
  }


  function recordsO()
  {
    if ($query = $this->getSQL())
    {
      $pos = strpos($query, "LIMIT");
      $qs = substr($query, 0, $pos);
      $tmp = new DB($this->dbId);
      $tmp->SQL($qs);
      $tmp->Query();

      return $tmp->Records();
    }

    return 0;
  }

  function records()
  {
    if ($query = $this->getSQL())
    {
      if (!$this->DB->resultReady())
      {
        $this->DB->SQL($query);
        $this->DB->Query();
      }

      $this->Records = $this->DB->Records();
      if ($this->Records > 0)
      {
        $this->DB->gotoRow($this->from);
      }

      return $this->Records;
    }

    return 0;
  }

  //  returns fetcharray
  function getRow()
  {
    $this->RowCounter++;

    if ($this->RowCounter > $this->perPage)
    {
      return false;
    }

    if ($result = $this->DB->nextRecord())
    {

      for ($b = 0; $b < count($this->columns); $b++)
      {

        $column = $this->columns[$b];
        $prop = array_merge($this->getPropArray($column["name"]), $column["options"]);

        $data[$column["name"]] = $prop;
        $data[$column["name"]]["field"] = $column["name"];
        $data[$column["name"]]["value"] = $result[$column["name"]];

      }

      return $data;
    }
    else
    {
      return false;
    }
  }

  function printHeader($disableEdit = true)
  {

    // rebuild querystring :  zonder sort, desc  zonder page
    foreach ($this->queryString as $keyname => $value)
    {
      if ($keyname != "sort" && $keyname != "direction" && $keyname != "page")
      {
        $str .= "&" . urlencode($keyname) . "=" . urlencode($value);
      }
    }

    //	$colDef = "<colgroup>";
    $header = "\n<thead>\n<tr class='list_kopregel'>";

    foreach ($this->columns as $colomnData)
    {
      $options = $colomnData["options"];
      $field = $colomnData["name"];

      $options = array_merge($this->getPropArray($field), $options);

      if (!$options["hideColumn"])
      {
        /** Sorter richting bepalen **/
        $sortDirection = '';
        if (isset ($this->sort[$field]))
        {
          $sortDirection = $this->sort[$field];
        }

        $dragClass = '';
        if (isset($options['fixed']) && $options['fixed'] === true)
        {
          $dragClass = 'notdraggable';
        }

        $tdSection = "\n\t<th data-sort=\"" . $sortDirection . "\" data-field=\"" . $field . "\" class='headerTD " . $options["widthClass"] . " " . $options["headerClass"] . " " . $dragClass . "' title='" . $options["description"] . "'>

					<div style=\"display: -webkit-inline-box;\">
        ";
        // $colDef .= "\n\t<col class='".$options['widthClass']."' title='$field' />";

        if ($options["sort"])
        {

          $tdSection .= '
					<div class="sortableIcons" style="width: 15px;text-align: left;">
						<i data-sortdirection="none" class="sortColumn fa fa-sort"></i>
						<i data-sortdirection="ASC" class="' . ($sortDirection !== 'ASC'?'hidden':'') . ' sortColumn fa fa-sort-asc"></i>
						<i data-sortdirection="DESC" class="' . ($sortDirection !== 'DESC'?'hidden':'') . ' sortColumn fa fa-sort-desc"></i>
					</div>
					';


//          $tdSection .=  "<a href=\"?".$str."&sort[]=".$fieldName."&direction[]=".$dir."&selectie=".$this->searchString."\"><i class=\"fa fa-battery-quarter\" aria-hidden=\"true\"></i>".maakKnop('sort_az_descending.png',array('size'=>16))."</a>&nbsp;";
        }
        $tdSection .= '<div>' . (($options["descriptionShort"] <> "")?$options["descriptionShort"]:$options["description"]) . '</div>';
        $tdSection .= "</div></th>";
        $header .= $tdSection;
      }
    }

    // $colDef .="\n</colgroup>";
    $header .= "\n<tr>\n</thead>";


    return $header;
  }

  function buildRow($data)
  {
    $ft = new AE_cls_formatter(",", ".");

    $trClass = 'dataRow';
    if (isset($data[0]['trClass']))
    {
      $trClass = $data[0]['trClass'];
    }

    $output = "\n<tr class='" . $trClass . "'>\n";

    $keyed = '';
    foreach ($data as $key => $row)
    {
      $keyed .= " | " . $key;
      if (!isset($row["linkId"]) AND $key == "id")
      {
        $row["linkId"] = $row["value"];
        $id = $row["linkId"];
      }
      if (is_array($row))
      {
        if (isset ($row["displayFormat"][0]) && $row["displayFormat"][0] == "@")  // formatting defined
        {
          $printdata = $ft->format($row["displayFormat"], $row["value"]);
        }
        else
        {
          $printdata = $ft->format("@S{0}", $row["value"]);  // output ongeformateerd
        }

        if (isset($row["links"]) && count($row["links"]) > 0)
        {

          $printdata = "<button class='iBtn' id='extra_$id'><img src='../images/16/information.png'/></button> " . $printdata;
        }

        if (isset ($row["type"]))
        {
          switch ($row["type"])
          {
            case "checkbox" :
              $printdata = imagecheckbox($row["value"]);
              break;
            default :
          }
        }
        $style = $row["formatClass"];
        if (isset($row["type"]) && strtolower($row["type"]) == "number")
        {
          $style = ($row["negativeClass"] <> "" AND $row["value"] < 0)?$row["negativeClass"]:$row["formatClass"];
        }
        if (!isset($row["hideColumn"]) || !$row["hideColumn"])
        {
          //build headerList
//          debug($this->orderBreak);
          $group = '';
          foreach ($this->orderBreak as $key => $headerData)
          {
            if (!empty($group))
            {
              $group .= ';';
            }
            $group .= '' . $key . '-' . $headerData['name']['value'];
          }

          $output .= "\t<td " . (isset($row["colspan"])?$row["colspan"]:'') . " data-group=\"" . $group . "\" data-field=\"" . (isset ($row['field'])?$row['field']:'') . "\" data-value='" . $row["value"] . "' class='" . $style . "' >"; //
          $output .= $printdata . " &nbsp;";
          $output .= "</td>\n";
        }

      }
    }
    $output .= "\n</tr>\n";

    return $output;


  }

  function printRow($template = "")
  {
    if ($data = $this->getRow())
    {
      return $this->buildRow($data, $template, "");
    }

    return false;
  }


  function fillTemplate($data, $template)
  {
    return $this->buildRow($data, $template, "");
  }

  //todo: moet excel uitvoer mogelijk zijn in deze class??
  function setXLS($title = '')
  {
    include_once("AE_cls_xls.php");
    $this->xlsData = array();
    $this->xls = new AE_xls();
    $this->selectAllPage();

    for ($x = 0; $x < count($this->columns); $x++)
    {
      $this->xlsData[0][] = array($this->columns[$x]['name'], 'header');
    }

    $x = 1;
    while ($data = $this->getRow())
    {
      foreach ($data as $key => $dataArray)
      {
        $this->xlsData[$x][] = array($dataArray['value'], 'body');
      }
      $x++;
    }
    $this->xls->excelOpmaak['header'] = array('setAlign' => 'centre', 'setBgColor' => '22', 'setBorder' => '1');
    $this->xls->excelOpmaak['body'] = array('setBorder' => '1');
    $this->xls->setColumn[] = array(0, 0, 6);
    $this->xls->setColumn[] = array(2, 5, 12);
    $this->xls->setData($this->xlsData);
  }

  function getXLS()
  {
    $this->xls->OutputXls('xls_' . date('Ymd') . '.xls');

  }

  function selectAllPage()
  {
    $this->DB = new DB($this->dbId);
    $query = $this->getSQL();
    $this->DB->SQL($query);

    return $this->DB->Query();
  }

  /**
   * zet de volgorde van het rapport
   * @param array $orderArray
   */
  function setOrderBreak($orderArray = array())
  {
    $this->orderBreakArray = $orderArray;
    foreach ($this->orderBreakArray as $item)
    {
      $this->orderBreak[$item]["previous"] = "empty";
      $this->orderBreak[$item]["total"] = 0;
      $this->orderBreak[$item]["name"] = 0;
    }

  }

  /**
   *  bepaal ordervolgorde, totaliseerbare kolom etc
   */
  function initRows()
  {

    // reset arrays
    $seenFields = array();
    $this->visibleColumns = 0;
    $this->sumColumns = array();
    $this->sumRowTotals = array();
    $this->reportTotals = array();
    foreach ($this->mainObject->data as $fieldName => $properties)
    {
      ////debug($properties,$fieldName);
      if (isset($this->columnExtraData["$fieldName"]))  // als er opties gezet zijn in addColumn deze samenvoegen
      {
        $properties = array_merge($properties, $this->columnExtraData["$fieldName"]);
      }
      ////debug($properties["sumTotal"],$fieldName);
      if ($properties["sumTotal"] == true)
      {
        $this->sumColumns[$fieldName] = array("class" => $properties["sumClass"], "format" => $properties["sumFormat"]);
      }
      //$seenFields[] = $field;

    }
    $this->visibleColumns = count($this->columns);
    foreach ($this->orderBreakArray as $sortColumn)
    {
      //if (!in_array($sortColumn, $seenFields))
      //{
      $this->addColumn($this->defaultObject, $this->sortData[$sortColumn]["orderBreakField"], array("hideColumn" => true));
      $this->addColumn($this->defaultObject, $this->sortData[$sortColumn]["descriptionField"], array("hideColumn" => true));
      //}
    }
  }

  /**
   * stel de order blokken in als <tbody>
   * @param $startOn
   * @param $endBefore
   */
  function setOrderBlock($startOn, $endBlockOn)
  {
    $this->startOn = $startOn;
    $this->endBlockOn = $endBlockOn;
  }

  /**
   * detecteer order overgang om subtotalen te kunnen bepalen
   */
  function checkOrderBreak($data, $breakField, $prevBreak = "")
  {

    $fldName = $this->sortData[$breakField]["orderBreakField"];

    if ($this->orderBreak[$breakField]["previous"] <> $data[$fldName]["value"] || $this->orderBreak[$breakField]["printed"] == true)
    {


      if ($this->orderBreak[$breakField]["previous"] <> "empty")
      {
        if ($prevBreak <> "")
        {
          $this->printOrderBreakFooter($data, $prevBreak);  // print de footer
          $this->orderBreak[$prevBreak]["printed"] = true;
        }
        $this->printOrderBreakFooter($data, $breakField);  // print de footer
      }

      if ($this->endBlockOn == $breakField)
      {
        echo '</tbody>';
      }
      if ($this->startOn == $breakField)
      {
        echo '<tbody>';
      }

      $this->breakTotals = array();                                                                         // reset de break totals
      $this->orderBreak[$breakField]["previous"] = $data[$fldName]["value"];                                // zet nieuwe break value
      $this->orderBreak[$breakField]["name"] = $data[$this->sortData[$breakField]["descriptionField"]]; // haal omschrijving op
      $this->orderBreak[$breakField]["total"] = array();                                                 // reset break totals
      $this->orderBreak[$breakField]["printed"] = false;                                                   // printflag
      foreach ($this->sumColumns as $sumField => $properties)
      {
        $this->orderBreak[$breakField]["total"][$sumField] = $data[$sumField]["value"];                     // vul de breaktotals met de huidige regel
      }
      $this->printOrderBreakHeader($breakField);
    }
    else
    {
      foreach ($this->sumColumns as $sumField => $properties)
      {
        $this->orderBreak[$breakField]["total"][$sumField] += $data[$sumField]["value"];          // update de breaktotals met de huidige regel
      }
    }
  }

  function orderBreakEnd($data)
  {

    //debug($this->orderBreak);

    $start = count($this->orderBreakArray) - 1;
    //debug($this->orderBreakArray, $start);
    for ($x = $start; $x >= 0; $x--)
    {
      //debug($this->orderBreakArray[$x],$x);
      $this->printOrderBreakFooter($data, $this->orderBreakArray[$x]);
    }
  }

  /**
   * print de order overgang kop
   */
  function printOrderBreakHeader($breakField)
  {
    $subTotalRow[] = array("value"       => "" . $this->orderBreak[$breakField]["name"]["value"],
                           "field"       => $breakField,
                           "formatClass" => $this->sortData[$breakField]["headerClass"] . ' groupHeader',
                           "colspan"     => "colspan='" . ($this->visibleColumns) . "'",
                           "trClass"     => 'headerRow');
    echo $this->buildRow($subTotalRow);
  }

  /**
   * print de order overgang voet
   */
  function printOrderBreakFooter($data, $breakField)
  {

    if ($this->orderBreak[$breakField]["printed"])
    {
      return;   // was al eerder geprint
    }
    //debug($this->orderBreak[$breakField]);
    $subTotalRow = array();
    foreach ($data as $field => $properties)
    {

      if ($field === 'id')
      {
        continue;
      }

      if (count($subTotalRow) == 0)   // eerste kolom
      {
        $subTotalRow[] = array("value" => "Subtotaal: <b>" . $this->orderBreak[$breakField]["name"]["value"] . "</b>", "formatClass" => "sumClass", "trClass" => 'footerRow');
        continue;
      }

      /**
       * Controlleren of de header aan of uit staat
       * Anders ook de totalen niet tonen
       */
      $inHeader = false;
      foreach ($this->columns as $column)
      {
        if ($column['name'] == $field)
        {
          if (
            !isset ($column['options']['hideColumn'])
            || (isset($column['options']['hideColumn']) && $column['options']['hideColumn'] === false)

          )
          {
            /** Header gevonden en deze mag getoond worden **/
            $inHeader = true;
          }
        }
      }
      /** Geen header gevonden of header is verborgen skip deze column **/
      if ($inHeader === false)
      {
        continue;
      }

      if (array_key_exists($field, $this->sumColumns))
      {
        $fldClass = ($this->orderBreak[$breakField]["total"][$field] < 0)?$properties["sumClass"] . "Negative":$properties["sumClass"];

        $subTotalRow[] = array(
          "field"         => $field,
          "value"         => $this->orderBreak[$breakField]["total"][$field],
          "formatClass"   => $field . ' ' . $fldClass,
          "displayFormat" => $properties["sumFormat"],
          "hideColumn"    => false,
          "trClass"       => 'footerRow');
      }
      else
      {
        $subTotalRow[] = array(
          "field"       => $field,
          "value"       => " ",
          "formatClass" => $field . ' ' . $properties["sumClass"],
          "hideColumn"  => false,
          "trClass"     => 'footerRow'
        );
      }


    }
    $this->debug = false;
//debug($subTotalRow);
    echo $this->buildRow($subTotalRow);
    $this->debug = false;
    // echo "<tr><td colspan='20'>subtotal:$breakField ".implode(", ",$this->breakTotals)."</td></tr>";
    ////debug($this->breakTotals,"breaktotals");
    ////debug($this->breakTotals,"breaktotals");
  }

  /**
   * print eindtotalen
   * @param $data
   */
  function printTotalFooter($data)
  {

    $endTotal = $this->mainObject->endTotal;
    foreach ($data as $field => $properties)
    {
      if ($field === 'id')
      {
        continue;
      }

      if (isset ($subTotalRow) && count($subTotalRow) == 0)   // eerste kolom
      {
        $subTotalRow[] = array("value" => "Rapport totalen: ", "formatClass" => $field . ' ' . $endTotal["class"], "trClass" => "footerRow footerEndRow",);
        continue;
      }

      /**
       * Controlleren of de header aan of uit staat
       * Anders ook de totalen niet tonen
       */
      $inHeader = false;
      foreach ($this->columns as $column)
      {
        if ($column['name'] == $field)
        {
          if (
            !isset ($column['options']['hideColumn'])
            || (isset($column['options']['hideColumn']) && $column['options']['hideColumn'] === false)

          )
          {
            /** Header gevonden en deze mag getoond worden **/
            $inHeader = true;
          }
        }
      }
      /** Geen header gevonden of header is verborgen skip deze column **/
      if ($inHeader === false)
      {
        continue;
      }

      if (array_key_exists($field, $this->reportTotals) AND !$properties["hideEndTotal"])
      {
        $fldClass = ($this->reportTotals[$field] < 0)?$endTotal["negativeClass"]:$endTotal["class"];

        $subTotalRow[] = array(
          "field"         => $field,
          "value"         => $this->reportTotals[$field],
          "trClass"       => "footerRow footerEndRow",
          "formatClass"   => $field . ' ' . $fldClass,
          "displayFormat" => $this->mainObject->endTotal["format"],
          "hideColumn"    => false);
      }
      else
      {
        $subTotalRow[] = array(
          "field"       => $field, "value" => " ",
          "trClass"     => "footerRow footerEndRow",
          "formatClass" => $field . ' ' . $endTotal["class"],
          "hideColumn"  => false);
      }


    }
    echo $this->buildRow($subTotalRow);
  }


  /**
   * totaliseer de kolommen voor het eindtotaal
   */
  function sumRowTotals($data)
  {
    foreach ($this->sumColumns as $field => $properties)
    {
      if (!isset ($this->reportTotals[$field]))
      {
        $this->reportTotals[$field] = 0;
      }
      $this->reportTotals[$field] += $data[$field]["value"];
    }
  }


}

?>